<?php

namespace App\Controller;

use App\Dto\SearchInput;
use App\Repository\ReadEventRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SearchController
{
    private ReadEventRepositoryInterface $repository;
    private SerializerInterface $serializer;

    public function __construct(
        ReadEventRepositoryInterface $repository,
        SerializerInterface $serializer
    ) {
        $this->repository = $repository;
        $this->serializer = $serializer;
    }

    /**
     * @Route(path="/api/search", name="api_search", methods={"GET"})
     */
    public function searchCommits(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $searchInput = $this->serializer->denormalize($request->query->all(), SearchInput::class);
        $errors = $validator->validate($searchInput);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new JsonResponse($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $countByType = $this->repository->countByType($searchInput);

        $data = [];

        if (count($countByType) > 0) {
            $data = [
                'meta' => [
                    'totalEvents' => $this->repository->countAll($searchInput),
                    'totalPullRequests' => $countByType['pullRequest'] ?? 0,
                    'totalCommits' => $countByType['commit'] ?? 0,
                    'totalComments' => $countByType['comment'] ?? 0,
                ],
                'data' => [
                    'events' => $this->repository->getLatest($searchInput),
                    'stats' => $this->repository->statsByTypePerHour($searchInput),
                ],
            ];
        }

        return new JsonResponse($data);
    }
}
