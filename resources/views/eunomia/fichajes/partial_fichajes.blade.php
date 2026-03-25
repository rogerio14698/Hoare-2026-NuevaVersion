                                    <table width="100%" cellpadding="10" cellspacing="0">
                                        <?php
                                            $fecha = '';
                                            $cont = 1;
                                            $entrada= '';
                                            $salida = '';
                                            setlocale(LC_TIME, 'es_ES.UTF-8');
                                        ?>
                                        @foreach($fichajes as $fichaje)
                                            @if(Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$fichaje->fecha)->format('d/m/Y')!=$fecha)
                                                <?php
                                                    $fecha_espanol = new DateTime($fichaje->fecha);
                                                    $fecha_espanol = strftime('%A %d/%m/%Y',$fecha_espanol->getTimestamp());
                                                    $fecha = Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$fichaje->fecha)->format('d/m/Y');
                                                ?>
                                                @if($cont>1)
                                                    </table></td></tr>
                                                @endif

                                                <tr style="background: transparent;">
                                                    <td style="background: {{$cont%2==0?'#DDDDDD':'#FFFFFF'}}">
                                                        <table width="100%">
                                                            <tr style="background: transparent;">
                                                                <td>Días: <strong>{!! $fecha_espanol !!}</strong></td>
                                                            </tr>
                                                <?php
                                                    $cont++;
                                                ?>
                                            @endif
                                            @if($fichaje->tipo=='entrada')
                                            <?php
                                                $entrada = new DateTime($fichaje->fecha);
                                            ?>
                                            <tr style="background: transparent;">
                                            @endif
                                                <td width="25%">
                                                    {!! ($fichaje->tipo=='entrada'?'Entrada: ':' Salida: ') . '<strong>' . Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$fichaje->fecha)->format('H:i') . '</strong>' !!}
                                                </td>
                                            @if($fichaje->tipo=='salida')
                                                <?php
                                                    $salida = new DateTime($fichaje->fecha);
                                                    $diferencia = $entrada->diff($salida);
                                                ?>
                                                <td width="50%">
                                                    Total: <strong>{{($diferencia->h<10?'0' . $diferencia->h:$diferencia->h) . ':' . ($diferencia->i<10?'0' . $diferencia->i:$diferencia->i)}}</strong>
                                                </td>
                                            </tr>
                                            @endif
                                        @endforeach
                                        @if(count($fichajes)>0)
                                            </table></td></tr>
                                        @endif
                                    </table>