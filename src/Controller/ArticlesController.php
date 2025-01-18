<?php

namespace App\Controller;

use App\Entity\Articulo;
use App\Repository\ArticuloRepository;
use App\Repository\CategoriaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/articulos', name: 'articulos_')]
class ArticlesController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ArticuloRepository $articuloRepository): JsonResponse
    {
        $articulos = $articuloRepository->findAll();

        $data = [];
        foreach ($articulos as $articulo) {
            $data[] = [
                'id' => $articulo->getId(),
                'nombre' => $articulo->getNombre(),
                'numeroArticulos' => $articulo->getCantidad(),
                'categoria' => $articulo->getCategoria()->getNombre(), // Obtener el nombre de la categoría
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/new', name: 'new', methods: ['POST'])]
    public function new(Request $request, ArticuloRepository $articuloRepository, CategoriaRepository $categoriaRepository): JsonResponse
    {
        try {
            //$data = json_decode($request->getContent(), true);
            $categoria_post = $request->request->get('categoria');
            //$categoria_post = 1;
            // Validar que la categoría existe
            $categoria = $categoriaRepository->findOneByid($categoria_post);
            if (!$categoria) {
                return new JsonResponse([
                    'mensaje' => 'Categoría no encontrada.'
                ], Response::HTTP_BAD_REQUEST);
            }

            $articulo = new Articulo();
            $nombre_post = $request->request->get('nombre');
            $articulo->setNombre($nombre_post);
            $articulo->setCategoria($categoria);
            $articulo->setCantidad($request->request->get('cantidad') ? $request->request->get('cantidad') : 0); // Valor por defecto 0

            $articuloRepository->save($articulo, true);

            return new JsonResponse([
                'mensaje' => 'Artículo creado correctamente.'
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return new JsonResponse([
                'mensaje' => 'Error al crear el artículo: ' . $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id_or_nombre}', name: 'delete', methods: ['DELETE'])]
public function delete(string $id_or_nombre, ArticuloRepository $articuloRepository): JsonResponse
{
    try {
        // Intentar buscar por ID primero
        if (is_numeric($id_or_nombre)) {
            $articulo = $articuloRepository->find($id_or_nombre);
        } else {
            $articulo = $articuloRepository->findOneByNombre($id_or_nombre);
        }

        if (!$articulo) {
            return new JsonResponse([
                'mensaje' => 'Artículo no encontrado.'
            ], Response::HTTP_NOT_FOUND);
        }

        $articuloRepository->remove($articulo, true);

        return new JsonResponse([
            'mensaje' => 'Artículo eliminado correctamente.'
        ]);

    } catch (\Exception $e) {
        return new JsonResponse([
            'mensaje' => 'Error al eliminar el artículo: ' . $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
#[Route('/{id}', name: 'edit', methods: ['PUT'])]
public function edit(Request $request, int $id, ArticuloRepository $articuloRepository, CategoriaRepository $categoriaRepository): JsonResponse
{
    try {
        $articulo = $articuloRepository->findOneById($id);

        if (!$articulo) {
            return new JsonResponse([
                'mensaje' => 'Artículo no encontrado.'
            ], Response::HTTP_NOT_FOUND);
        }
        $data = json_decode($request->getContent(), true);
        // Obtener los datos del formulario
        $nombre = $data['nombre'];
        $categoriaId = $data['categoria'];
        $numeroArticulos =$data['cantidad'];

        // Actualizar el nombre si se proporciona
        if ($nombre !== null) {
            $articulo->setNombre($nombre);
        }

        // Actualizar la categoría si se proporciona
        if ($categoriaId !== null) {
            $categoria = $categoriaRepository->findOneById($categoriaId);
            if (!$categoria) {
                return new JsonResponse([
                    'mensaje' => 'Categoría no encontrada.'
                ], Response::HTTP_BAD_REQUEST);
            }
            $articulo->setCategoria($categoria);
        }
        
        // Actualizar el número de artículos si se proporciona
        if ($numeroArticulos !== null) {
            $articulo->setCantidad($numeroArticulos);
        }

        $articuloRepository->save($articulo, true);

        return new JsonResponse([
            'mensaje' => 'Artículo actualizado correctamente.',
            'nombre' => $articulo->getNombre(),
            'canitidad' => $articulo->getCantidad(),
            'nombre_post' => $nombre,
            'canitidad_post' => $numeroArticulos
        ]);

    } catch (\Exception $e) {
        return new JsonResponse([
            'mensaje' => 'Error al actualizar el artículo: ' . $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

}