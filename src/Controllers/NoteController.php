<?php

namespace Trulyao\PhpJwt\Controllers;

use Exception;
use stdClass;
use Trulyao\PhpJwt\Models\Note;
use Trulyao\PhpJwt\Utils\CustomException;
use Trulyao\PhpJwt\Utils\InputHandler;
use Trulyao\PhpRouter\HTTP\Response as Response;
use Trulyao\PhpRouter\HTTP\Request as Request;
use Trulyao\PhpJwt\Utils\ResponseHandler as ResponseHandler;

class NoteController
{

    public static function createNote(Request $request, Response $response): Response
    {
        try {
            extract($request->body());

            $user = $request->data["user"];

            if (empty($title) || empty($content)) {
                throw new CustomException("All fields are required!", 400);
            }

            $title = InputHandler::normalizeString($title);
            $content = InputHandler::normalizeString($content);

            $note = new Note();
            $note->title = $title;
            $note->content = $content;
            $note->user_id = $user->id;
            $note->save();

            return ResponseHandler::success($response, "Note created!", 200, (array)$note);
        } catch (CustomException|Exception $e) {
            return ResponseHandler::error($response, $e);
        }
    }

    public static function getNote(Request $request, Response $response): Response
    {
        try {

            $note = self::getStdClass($request);

            return ResponseHandler::success($response, "Note found!", 200, (array)$note);
        } catch (CustomException|Exception $e) {
            return ResponseHandler::error($response, $e);
        }
    }

    public static function getNotes(Request $request, Response $response): Response
    {
        try {
            $user_id = $request->data["user"]->id;

            $notes = Note::findMany($user_id);

            if (!$notes) {
                throw new CustomException("You don't have any notes chief", 404);
            }

            return ResponseHandler::success($response, "Here you go!", 200, $notes);
        } catch (CustomException|Exception $e) {
            return ResponseHandler::error($response, $e);
        }
    }

    public static function deleteNote(Request $request, Response $response): Response
    {
        try {
            $note = self::getStdClass($request);
            $delete = Note::deleteOne($note->id);

            if (!$delete) {
                throw new CustomException("Something went wrong", 500);
            }

            return ResponseHandler::success($response, "Note deleted!");
        } catch (CustomException|Exception $e) {
            return ResponseHandler::error($response, $e);
        }
    }

    /**
     * @param Request $request
     * @return bool|stdClass
     * @throws CustomException
     */
    public static function getStdClass(Request $request): stdClass|bool
    {
        $note_id = $request->params("id");
        $user_id = $request->data["user"]->id;

        $_note_id = (int)$note_id;

        if (empty($_note_id)) {
            throw new CustomException("Note ID is required!", 400);
        }

        $note = Note::findOne($note_id);
        if (!$note) {
            throw new CustomException("Uh... this note doesn't exist", 404);
        }
        if ($note->user_id != $user_id) {
            throw new CustomException("You don't own this note chief!", 403);
        }
        return $note;
    }
}