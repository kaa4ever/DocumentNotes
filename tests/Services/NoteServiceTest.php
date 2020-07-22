<?php

namespace App\Tests\Services;

use App\Entity\Note;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NoteServiceTest extends WebTestCase
{
    /**
     * @test
     */
    public function must_return_note_when_it_is_create()
    {
        // Arrange
        $client = static::createClient();
        $note = new Note(1,"this is my text",1);

        // Act
        $client->request(
            'POST',
            '/notes',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($note)
        );

        // Assert
        $newNote = json_decode($client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($note->getText(), $newNote->text);

        $client->request('DELETE', '/resetdatabase');
    }

    /**
     * @test
     */
    public function must_return_all_notes()
    {
        // Arrange
        $client = static::createClient();
        $note = new Note(1,"this is my text",1);

        // Act
        $client->request(
            'POST',
            '/notes',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($note)
        );
        $client->request(
            'POST',
            '/notes',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($note)
        );

        $client->request(
            'GET',
            '/notes'
        );

        // Assert
        $newNotes = json_decode($client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(2, count($newNotes));

        $client->request('DELETE', '/resetdatabase');
    }

    /**
     * @test
     */
    public function must_find_the_note()
    {
        // Arrange
        $client = static::createClient();
        $note = new Note(1,"this is my text",1);

        // Act
        $client->request(
            'POST',
            '/notes',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($note)
        );
        $newNote = json_decode($client->getResponse()->getContent());
        $uri = sprintf('/notes/%s', $newNote->id);

        $client->request(
            'GET',
            $uri
        );

        // Assert
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('DELETE', '/resetdatabase');
    }

    /**
     * @test
     */
    public function must_give_404_when_note_dont_exits()
    {
        // Arrange
        $client = static::createClient();

        // Act

        $uri = sprintf('/notes/%s', 1);

        $client->request(
            'GET',
            $uri
        );

        // Assert
        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        $client->request('DELETE', '/resetdatabase');
    }

    /**
     * @test
     */
    public function must_give_404_when_update_no_note_dont_exits()
    {
        // Arrange
        $client = static::createClient();

        // Act

        $client->request(
            'PUT',
            '/notes'
        );

        // Assert
        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        $client->request('DELETE', '/resetdatabase');
    }

    /**
     * @test
     */
    public function must_return_updated_note()
    {
        // Arrange
        $client = static::createClient();

        // Act
        $note1 = new Note(1,"this is my text",1);

        // Act
        $client->request(
            'POST',
            '/notes',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($note1)
        );
        $newNote1 = json_decode($client->getResponse()->getContent());
        $newNote1->text ="updated text";

        $client->request(
            'PUT',
            '/notes',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($newNote1)
        );
        $newNote2 = json_decode($client->getResponse()->getContent());

        // Assert
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($newNote1->id, $newNote2->id);
        $this->assertNotEquals($newNote1, $newNote2);
        $client->request('DELETE', '/resetdatabase');
    }

    /**
     * @test
     */
    public function must_not_accept_wrong_http_method()
    {
        // Arrange
        $client = static::createClient();

        // Act
        $note1 = new Note(1,"this is my text",1);

        // Act
        $client->request(
            'POST',
            '/notes/1',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($note1)
        );

        // Assert
        $this->assertEquals(405, $client->getResponse()->getStatusCode());
    }
}
