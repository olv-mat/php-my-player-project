<?php

namespace Project\AluraPlay\Controller;

use Project\AluraPlay\Repository\VideoRepository;
use Project\AluraPlay\Entity\Video;
use League\Plates\Engine;
use PDO;

class VideosJsonController implements Controller
{
    private VideoRepository $repository;
    private string $requestMethod;
    private Engine $template;

    public function __construct(VideoRepository $repository, string $requestMethod, Engine $template)
    {
        $this->repository = $repository;
        $this->requestMethod = $requestMethod;
        $this->template = $template;
    }

    public function requestProcessing(): void
    {
        $videos = $this->repository->selectVideos();
        $videosArray = array_map(function ($video) {
            return $video->toArray();
        }, $videos);
        echo json_encode($videosArray);
    }

}
