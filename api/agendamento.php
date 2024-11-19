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
            $sql = "SELECT 
                        a.id_agendamentos, 
                        p.nome_paciente, 
                        m.nome_medico, 
                        a.paciente_sintomas, 
                        a.data_hora, 
                        a.status
                    FROM agendamentos a
                    JOIN pacientes p ON a.id_pacientes = p.id_pacientes
                    JOIN medicos m ON a.id_medicos = m.id_medicos";
            $stmt = $pdo->query($sql);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            echo json_encode(["error" => "Erro ao listar agendamentos: " . $e->getMessage()]);
        }
        break;

    case 'create':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id_medicos'], $data['id_pacientes'], $data['paciente_sintomas'], $data['data_hora'])) {
            echo json_encode(["error" => "Todos os campos obrigatórios devem ser preenchidos"]);
            exit;
        }

        try {
            $sql = "INSERT INTO agendamentos (id_medicos, id_pacientes, paciente_sintomas, data_hora, status) 
                    VALUES (:id_medicos, :id_pacientes, :paciente_sintomas, :data_hora, 'pendente')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id_medicos' => $data['id_medicos'],
                ':id_pacientes' => $data['id_pacientes'],
                ':paciente_sintomas' => $data['paciente_sintomas'],
                ':data_hora' => $data['data_hora'],
            ]);
            echo json_encode(["success" => "Agendamento criado com sucesso", "id" => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Erro ao criar agendamento: " . $e->getMessage()]);
        }
        break;

    case 'delete':
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;

        if (!$id) {
            echo json_encode(["error" => "ID do agendamento é obrigatório"]);
            exit;
        }

        try {
            $sql = "DELETE FROM agendamentos WHERE id_agendamentos = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            echo json_encode(["success" => "Agendamento deletado com sucesso"]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Erro ao deletar agendamento: " . $e->getMessage()]);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(["error" => "Ação inválida"]);
        break;
}