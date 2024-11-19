<?php
// Configurações de conexão
$host = 'localhost';        // endereço do servidor, geralmente localhost
$dbname = 'agendamento_medico';  // nome do banco de dados
$username = 'root';      // usuário do banco de dados
$password = '';        // senha do banco de dados

try {
    // Cria uma nova conexão PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Configura o PDO para lançar exceções em caso de erros
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Exibe mensagem de erro em caso de falha na conexão
    echo "Erro na conexão: " . $e->getMessage();
}
?>