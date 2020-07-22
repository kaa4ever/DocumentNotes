<?php


namespace App\Controller;


use App\Repository\NoteRepository;
use App\Services\NoteService;
use http\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


class NoteController extends AbstractController
{
    /**
     * @var NoteService
     */
    private $noteService;

    /**
     * @var NoteRepository
     */
    private $noteRepository;

    public function __construct(NoteService $noteService, NoteRepository $noteRepository)
    {
        $this->noteService = $noteService;
        $this->noteRepository = $noteRepository;
    }

    /**
     * @Route("/notes", methods={"POST"})
     */
    public function noteCreateAction(Request $request)
    {
        $note = $this->noteService->createNote($request);
        if ($note) {
            return new JsonResponse($note);
        }
        throw new BadRequestHttpException('couldn\'t create note');
    }

    /**
     * @Route("notes/{id}", methods={"GET"})
     */
    public function getNoteAction($id)
    {
        try{
            $note = $this->noteService->findNoteById($id);
            return new JsonResponse($note);
        }catch(Exception $e){
        }
    }

    /**
     * @Route("notes", methods={"GET"})
     */
    public function getAllNotesAction()
    {
        return new JsonResponse($this->noteService->getAllNote());
    }

    /**
     * @Route("notes/document/{id}", methods={"GET"})
     */
    public function getNotesOnDocumentAction($id)
    {
        $notes = $this->noteService->findNotesByDocumentId($id);
        if ($notes) {
            return new JsonResponse($notes);
        }
        $msg = sprintf('No notes found on document with is %s' , $id);
        throw new NotFoundHttpException($msg);
    }

    /**
     * @Route("notes", methods={"PUT"})
     */
    public function updateNote(Request $request)
    {
        try{
            $note = $this->noteService->updateNote($request);
            return new JsonResponse($note);
        }catch (Exception $e)
        {
        }
    }

    /**
     * This route is only purpes is to reset the database in between tests.
     * @Route("/resetdatabase")
     */
    public function resetDatabase()
    {
        $this->noteRepository->resetDatabase();
        return new Response('All notes delete', Response::HTTP_OK);

    }
}