<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainConterollerController extends AbstractController
{
    #[Route('/main', name: 'app_main_conteroller')]
    public function index(): Response
    {
        $fullName = 'Shuai zhang';
        $dataArray = [
            'username' => 'shuai',
            'age' => 18,
            'hobbies' => ['reading', 'coding', 'gaming'],
            'location' => [
                'city' => 'Beijing',
                'country' => 'China'
            ]
        ];
        return $this->render('index.html.twig', [
            'dataArray' => $dataArray,
            'fullName' => $fullName
        ]);
    }

    #[Route('/test-connection', name: 'app_test_connection')]
    public function testConnection(): Response
    {
        try {
            $conn = new PDO(
                "sqlsrv:Server=your-server;Database=your-database",
                "your-username",
                "your-password",
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_UTF8
                )
            );
            echo "Connected successfully";
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        return $this->render('test_connection.html.twig');
    }
}
