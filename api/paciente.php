<?php

include '../connect.php';

$action = isset($_GET['action']) ? $_GET['action'] : null;

if (!$action) {
    http_response_code(400);
    echo json_encode(["error" => "Ação é obrigatória"]);
    exit;
}

switch ($action) {
    case 'list':
        try {
            $sql = "SELECT * FROM pacientes";
            $stmt = $pdo->query($sql);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            echo json_encode(["error" => "Erro ao listar pacientes: " . $e->getMessage()]);
        }
        break;

    case 'create':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['nome_paciente'], $data['data_nascimento'], $data['genero'], $data['cpf'], $data['contato'])) {
            echo json_encode(["error" => "Todos os campos obrigatórios devem ser preenchidos"]);
            exit;
        }

        try {
            $sql = "INSERT INTO pacientes (nome_paciente, data_nascimento, genero, cpf, contato) 
                    VALUES (:nome_paciente, :data_nascimento, :genero, :cpf, :contato)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome_paciente' => $data['nome_paciente'],
                ':data_nascimento' => $data['data_nascimento'],
                ':genero' => $data['genero'],
                ':cpf' => $data['cpf'],
                ':contato' => $data['contato'],
            ]);
            echo json_encode(["success" => "Paciente criado com sucesso", "id" => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Erro ao criar paciente: " . $e->getMessage()]);
        }
        break;

    case 'update':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id'], $data['nome_paciente'], $data['data_nascimento'], $data['genero'], $data['cpf'], $data['contato'])) {
            echo json_encode(["error" => "Todos os campos obrigatórios devem ser preenchidos"]);
            exit;
        }

        try {
            $sql = "UPDATE pacientes 
                    SET nome_paciente = :nome_paciente, data_nascimento = :data_nascimento, genero = :genero, cpf = :cpf, contato = :contato 
                    WHERE id_pacientes = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id' => $data['id'],
                ':nome_paciente' => $data['nome_paciente'],
                ':data_nascimento' => $data['data_nascimento'],
                ':genero' => $data['genero'],
                ':cpf' => $data['cpf'],
                ':contato' => $data['contato'],
            ]);
            echo json_encode(["success" => "Paciente atualizado com sucesso"]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Erro ao atualizar paciente: " . $e->getMessage()]);
        }
        break;

    case 'delete':
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;

        if (!$id) {
            echo json_encode(["error" => "ID do paciente é obrigatório"]);
            exit;
        }

        try {
            $sql = "DELETE FROM pacientes WHERE id_pacientes = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            echo json_encode(["success" => "Paciente deletado com sucesso"]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Erro ao deletar paciente: " . $e->getMessage()]);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(["error" => "Ação inválida"]);
        break;
}