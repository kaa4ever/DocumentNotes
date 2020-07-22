<?php

namespace App\Services;

use App\Entity\Note;
use App\Repository\NoteRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NoteService
{
    /**
     * @var NoteRepository
     */
    private $noteRepository;

    public function __construct(NoteRepository $noteRepository)
    {
        $this->noteRepository = $noteRepository;
    }

    public function createNote(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $note = new Note(
            $data['user'],
            $data['text'],
            $data['document'],
            new \DateTime(),
            null
        );
        $this->noteRepository->save($note);
        return $note;
    }

    /**
     * Find Note based on note id.
     * @param $id
     * @return Note
     */
    public function findNoteById($id)
    {
        $note = $this->noteRepository->findOneBy(['id' => $id]);
        if ($note) {
            return $note;
        } else {
            throw new NotFoundHttpException('The note does not exist');
        }
    }

    /**
     * return all notes as a array
     * @return Note[]
     */
    public function getAllNote()
    {
        return $this->noteRepository->findAll();
    }

    /**
     * return all notes on a document as Note array
     * @param $id
     * @return Note[]
     */
    public function findNotesByDocumentId($id)
    {
        $note = $this->noteRepository->findBy(['document' => $id]);
        if (!$note) {
            $msg = sprintf('No note found with id %s', $id);
            throw new NotFoundHttpException($msg);
        }
        return $note;
    }

    /**
     * Update Note
     *
     * @param Request $request
     * @return Note
     */
    public function updateNote(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $note = $this->findNoteById($data['id']);
        $note->setText($data['text']);
        $note->setUpdated(new \DateTime());
        $note->setUser($data['user']);
        $this->noteRepository->save($note);
        return $note;
    }
}