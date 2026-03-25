<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Carbon\Carbon;
use App\CommentUser;

use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Caché en memoria de los IDs de rol del usuario (evita N+1 queries).
     */
    protected $cachedRoleIds = null;

    /**
     * Caché en memoria de los IDs de permiso del usuario (directos + por rol).
     */
    protected $cachedPermissionSlugs = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'dni',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Los atributos que deben ser casteados.
     */
    protected $casts = [
        //
    ];

    /**
     * Accessor para dni: intenta descifrar el valor.
     * Si falla (valor en texto plano antiguo), devuelve el valor tal cual.
     */
    public function getDniAttribute($value)
    {
        if ($value === null) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException $e) {
            return $value;
        }
    }

    /**
     * Mutator para dni: cifra el valor al guardarlo.
     */
    public function setDniAttribute($value)
    {
        $this->attributes['dni'] = $value !== null ? Crypt::encryptString($value) : null;
    }

    public function tasks()
    {
        return $this->belongsToMany('App\Task')->withTimestamps();
    }

    public function todotasks()
    {
        return $this->belongsToMany('App\TodoTask')->withTimestamps();
    }

    public function comments()
    {
        return $this->belongsToMany('App\Comment');
    }

    public function getAvatarAttribute($value)
    {
        return $value ?: 'sinavatar.jpg';
    }

    public function roles_usuario()
    {
        return $this->hasMany('App\RolesUsuario', 'user_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Customer', 'customer_id', 'id');
    }

    public function departamento()
    {
        return $this->belongsTo('App\Departamento', 'role_id', 'id');
    }

    public function isRole($role)
    {
        $rol = Rol::where('slug', $role)->first();
        if (!$rol) return false;

        return in_array($rol->id, $this->getRoleIds());
    }

    public function isPermission($permission)
    {
        $permiso = Permission::where('slug', $permission)->first();
        if (!$permiso) return false;

        return PermissionsUsuario::where('permission_id', $permiso->id)
            ->where('user_id', $this->id)
            ->exists();
    }

    /**
     * Obtiene los IDs de rol del usuario y los cachea en memoria para la request.
     */
    protected function getRoleIds()
    {
        if ($this->cachedRoleIds === null) {
            $this->cachedRoleIds = RolesUsuario::where('user_id', $this->id)
                ->pluck('role_id')
                ->toArray();
        }
        return $this->cachedRoleIds;
    }

    /**
     * Carga todos los slugs de permisos del usuario (vía roles + directos) una sola vez.
     */
    protected function loadPermissionSlugs()
    {
        if ($this->cachedPermissionSlugs !== null) return;

        $roleIds = $this->getRoleIds();

        // Permisos asignados a través de los roles del usuario
        $porRol = Permission::join('permission_role', 'permissions.id', '=', 'permission_role.permission_id')
            ->whereIn('permission_role.role_id', $roleIds)
            ->pluck('permissions.slug')
            ->toArray();

        // Permisos asignados directamente al usuario
        $directos = Permission::join('permission_user', 'permissions.id', '=', 'permission_user.permission_id')
            ->where('permission_user.user_id', $this->id)
            ->pluck('permissions.slug')
            ->toArray();

        $this->cachedPermissionSlugs = array_unique(array_merge($porRol, $directos));
    }

    public function compruebaSeguridad($permission)
    {
        $this->loadPermissionSlugs();
        return in_array($permission, $this->cachedPermissionSlugs);
    }

    /**
     * Comprueba si el usuario tiene permiso de mostrar el módulo asignado al elemento de menú ($modulo).
     */
    public function compruebaSeguridadMenu($modulo)
    {
        $permiso = Permission::where('model', $modulo)->where('permission_type', 'mostrar')->first();
        if (!$permiso) return false;

        $this->loadPermissionSlugs();
        return in_array($permiso->slug, $this->cachedPermissionSlugs);
    }

    public function fichajes()
    {
        return $this->HasMany('App\Fichaje', 'user_id', 'id')->orderBy('fecha');
    }

    public function empresa()
    {
        return $this->belongsTo('App\Empresa', 'empresa_id', 'id');
    }

    public function nactive_projects()
    {
        return Project::where('user_id', $this->id)
            ->where('estado_proyecto', '<>', '4')->get()->count();
    }

    public function nactive_tasks()
    {
        return TaskUser::join('tasks', 'task_user.task_id', 'tasks.id')
            ->where('user_id', $this->id)
            ->where('estado_tarea', '<>', '4')->get()->count();
    }

    public function dias_en_empresa()
    {
        $fechaEmision = Carbon::parse($this->created_at);
        $fechaExpiracion = Carbon::parse(date('Y-m-d H:i:s'));

        return $fechaExpiracion->diffInDays($fechaEmision);
    }

    public function ncomentarios()
    {
        return CommentUser::where('user_id', $this->id)->count();
    }

    public function userdata()
    {
        return $this->hasOne('App\Userdata', 'user_id', 'id');
    }

    public function devuelve_avatar()
    {
        if ($this->avatar != '')
            return asset('images/avatar/' . $this->avatar);
    }

    // Accessor para obtener la URL del avatar del usuario
    public function getAvatarUrlAttribute()
    {
        // Si no tiene avatar, usamos 'sinavatar.jpg' como valor por defecto
        $file = $this->avatar ?: 'sinavatar.jpg'; 
        $path = public_path('images/avatar/' . $file);

        // Devuelve la URL del avatar o del avatar por defecto
        return file_exists($path)
            ? asset('images/avatar/' . $file)
            : asset('images/avatar/sinavatar.jpg'); 
    }

    public function devuelveMesLetra($mes)
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo',
            4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre',
            10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        return $meses[$mes] ?? null;
    }

}
