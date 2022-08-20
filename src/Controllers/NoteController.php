<?php

namespace Trulyao\PhpJwt\Controllers;

use Exception;
use PDO;
use Trulyao\PhpJwt\Models\Note;
use Trulyao\PhpJwt\Utils\CustomException;
use Trulyao\PhpRouter\HTTP\Response as Response;
use Trulyao\PhpRouter\HTTP\Request as Request;
use Trulyao\PhpJwt\Utils\ResponseHandler as ResponseHandler;
use Trulyao\PhpJwt\Services\Connection as Connection;

class NoteController {

    private PDO $conn;
    private Connection $pdo;

    public static function createNote(Request $request, Response $response): Response
    {
        try {
            extract($request->body());

            $user = $request->data["user"];

            if (empty($title) || empty($content)) {
                throw new CustomException("All fields are required!", 400);
            }

            $note = new Note();
            $note->title = $title;
            $note->content = $content;
            $note->user_id = $user->id;
            $note->save();

            return ResponseHandler::success($response, "Note created!",200, (array) $note);
        } catch (CustomException|Exception $e) {
            return ResponseHandler::error($response, $e);
        }
    }

    public static function getNote(Request $request, Response $response): Response
    {
        try {

            $note_id = $request->params("id");
            $user_id = $request->data["user"]->id;

            $note = Note::findOne($note_id);

            if(!$note) {
                throw new CustomException("Uhhh... this note doesn't exist", 404);
            }

            if($note->user_id != $user_id) {
                throw new CustomException("You don't own this note chief!", 403);
            }

            return ResponseHandler::success($response, "Note found!",200, (array) $note);
        } catch (CustomException|Exception $e) {
            return ResponseHandler::error($response, $e);
        }
    }

    public static function getNotes(Request $request, Response $response): Response
    {
        try {
            $user_id = $request->data["user"]->id;
            $notes = Note::findMany($user_id);

            if(!$notes) {
                throw new CustomException("You don't have any notes chief", 404);
            }

            return ResponseHandler::success($response, "Here you go!",200, $notes);
        } catch (CustomException|Exception $e) {
            return ResponseHandler::error($response, $e);
        }
    }

    public static function deleteNote (Request $request, Response $response): Response
    {
        try {
            $note_id = $request->params("id");
            $user_id = $request->data["user"]->id;
            $note = Note::findOne($note_id);
            if(!$note) {
                throw new CustomException("Uhhh... this note doesn't exist", 404);
            }
            if($note->user_id != $user_id) {
                throw new CustomException("You don't own this note chief!", 403);
            }
            $delete = Note::deleteOne($note->id);

            if(!$delete) {
                throw new CustomException("Something went wrong", 500);
            }

            return ResponseHandler::success($response, "Note deleted!",200);
        } catch (CustomException|Exception $e) {
            return ResponseHandler::error($response, $e);
        }
    }
}