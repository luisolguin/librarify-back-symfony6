<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Repository\CategoriaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categorias', name: 'categorias_')]
class CategoriasController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(CategoriaRepository $categoriaRepository): JsonResponse
    {
        $categorias = $categoriaRepository->findAll();

        // Convertir las categorías a un array de arrays asociativos
        $data = [];
        foreach ($categorias as $categoria) {
            $data[] = [
                'id' => $categoria->getId(),
                'nombre' => $categoria->getNombre(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/new', name: 'new', methods: ['POST'])]
    public function new(Request $request, CategoriaRepository $categoriaRepository): JsonResponse
    {
        try {
            //$data = json_decode($request->getContent(), true);
            
            $nombre = $request->request->get('nombre');
            $categoria = new Categoria();
            $categoria->setNombre($nombre);

            $categoriaRepository->save($categoria, true);

            return new JsonResponse([
                'mensaje' => 'Categoría creada correctamente.'
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return new JsonResponse([
                'mensaje' => 'Error al crear la categoría: ' . $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
    
}