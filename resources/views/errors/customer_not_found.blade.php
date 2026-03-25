<!DOCTYPE html>
<html>
<head>
    <title>Cliente no encontrado</title>
    <style>
        .error-container {
            padding: 20px;
            text-align: center;
            font-family: Arial, sans-serif;
        }
        .error-message {
            color: #d9534f;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .error-details {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-message">
            <strong>Cliente no encontrado</strong>
        </div>
        <div class="error-details">
            El cliente con ID {{ $customer_id }} no existe o no tiene permisos para acceder a él.
        </div>
        <div style="margin-top: 20px;">
            <button onclick="window.parent.location.reload();" class="btn btn-primary">
                Recargar página
            </button>
        </div>
    </div>
</body>
</html>