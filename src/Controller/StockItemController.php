<?php

namespace App\Controller;

use App\Repository\StockItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StockItemController extends AbstractController
{
    #[Route('/api/get-stock', name:'stock_item-get', methods: ['GET'])]
    public function index(Request $request, StockItemRepository $stockItemRepository): JsonResponse {
        $ean = $request->query->get('ean');
        $mpn = $request->query->get('mpn');

        if (empty($mpn) && empty($ean)) {
            return new JsonResponse(['error' => 'At least one of mpn or ean must be provided'], 400);
        }
        $items = $stockItemRepository->findByMpnOrEan($mpn ?: null, $ean ?: null);
        $data = array_map(fn($i) => $i->toArray(), $items);

        return new JsonResponse($data, 200, ['Content-Type' => 'application/json']);
    }
}