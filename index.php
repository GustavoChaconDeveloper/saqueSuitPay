<?php
$clientId = 'Chave CI';
$clientSecret = 'Chave Cs';

function sendTestPix($pixKey, $clientId, $clientSecret) {
    $curl = curl_init();

    // Valor do PIX de teste entre R$ 0,05 e R$ 0,10
    $testAmount = rand(5, 10) / 100.0;

    $payload = json_encode([
        "value" => $testAmount,
        "key" => $pixKey,
        "typeKey" => "phoneNumber"
    ]);

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://ws.suitpay.app/api/v1/gateway/pix-payment',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            "ci: $clientId",
            "cs: $clientSecret"
        ),
    ));

    // Desabilitar a verificação SSL (apenas para testes; não use em produção)
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        return ['error' => curl_error($curl)];
    } else {
        $responseDecoded = json_decode($response, true);

        // Apenas verificar se a resposta não é nula e contém idTransaction
        if (isset($responseDecoded['idTransaction'])) {
            return ['success' => true, 'testAmount' => $testAmount];
        } else {
            return ['error' => $responseDecoded['message'] ?? 'Erro desconhecido'];
        }
    }

    curl_close($curl);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $result = null;

    if ($action === 'sendTestPix') {
        $pixKey = $_POST['pixKey'];
        $result = sendTestPix($pixKey, $clientId, $clientSecret);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empresa - Teste PIX</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .btn-custom {
            margin: 10px;
        }
        .form-container {
            display: none;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }
        .form-container.active {
            display: block;
            opacity: 1;
        }
        .form-container.hidden {
            display: none;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 4px;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
    <script>
        function showForm(formId) {
            document.querySelectorAll('.form-container').forEach(form => {
                form.classList.remove('active');
                form.classList.add('hidden');
            });
            document.getElementById(formId).classList.remove('hidden');
            document.getElementById(formId).classList.add('active');
        }

        function formatPhone(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.length <= 10) {
                value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
                value = value.replace(/(\d{5})(\d{0,4})/, '$1-$2');
            } else {
                value = value.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
            }
            input.value = value;
        }

        function formatCpf(input) {
            let value = input.value.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/, '$1.$2.$3-$4');
            input.value = value;
        }

        function sanitizePhone(input) {
            return input.replace(/\D/g, '');
        }

        function sanitizeCpf(input) {
            return input.replace(/\D/g, '');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const phoneInput = document.getElementById('pixKeyPhone');
            const cpfInput = document.getElementById('pixKeyCpf');

            phoneInput.addEventListener('input', function () {
                formatPhone(phoneInput);
            });

            cpfInput.addEventListener('input', function () {
                formatCpf(cpfInput);
            });

            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function () {
                    const phoneValue = phoneInput.value;
                    const cpfValue = cpfInput.value;

                    if (phoneValue) {
                        phoneInput.value = sanitizePhone(phoneValue);
                    }

                    if (cpfValue) {
                        cpfInput.value = sanitizeCpf(cpfValue);
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Empresa - Saque PIX</h1>
        <div class="text-center">
            <button class="btn btn-primary btn-custom" onclick="showForm('phone-form')">Saque por Telefone</button>
            <button class="btn btn-primary btn-custom" onclick="showForm('cpf-form')">Saque por CPF</button>
        </div>

        <div id="forms-container">
            <form method="POST" id="phone-form" class="form-container">
                <div class="form-group">
                    <label for="pixKeyPhone">Chave PIX (Telefone):</label>
                    <input type="text" id="pixKeyPhone" name="pixKey" class="form-control" placeholder="Digite o telefone" required>
                </div>
                <input type="hidden" name="action" value="sendTestPix">
                <button type="submit" class="btn btn-success">Enviar PIX de Teste</button>
            </form>

            <form method="POST" id="cpf-form" class="form-container">
                <div class="form-group">
                    <label for="pixKeyCpf">Chave PIX (CPF):</label>
                    <input type="text" id="pixKeyCpf" name="pixKey" class="form-control" placeholder="Digite o CPF" required>
                </div>
                <input type="hidden" name="action" value="sendTestPix">
                <button type="submit" class="btn btn-success">Enviar PIX de Teste</button>
            </form>
        </div>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($result)) : ?>
            <div class="alert <?php echo isset($result['error']) ? 'alert-danger' : 'alert-success'; ?>" role="alert">
                <?php if (isset($result['error'])) : ?>
                    <p><i class="fas fa-exclamation-circle"></i> Erro: <?php echo htmlspecialchars($result['error']); ?></p>
                <?php else : ?>
                    <p><i class="fas fa-check-circle"></i> PIX de teste enviado com sucesso!</p>
                    <p>Valor do PIX de teste: R$ <?php echo htmlspecialchars($result['testAmount']); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
