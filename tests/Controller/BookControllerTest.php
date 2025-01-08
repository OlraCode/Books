<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

final class BookControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/book/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Book::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Lista de Livros');

    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Salvar', [
            'book[title]' => 'Testing',
        ]);

        self::assertResponseRedirects('/book');

        self::assertSame(1, $this->repository->count([]));
    }

    public function testNewWithCover(): void
    {
        $this->client->request('GET', sprintf('%snew', $this->path));

        $this->client->submitForm('Salvar', [
            'book[title]' => 'TestWithCover',
            'book[cover]' => __DIR__ . '/teste.jpg'
        ]);

        /** @var Book */
        $book = $this->repository->findAll()[0];

        self::assertResponseRedirects('/book');

        self::assertNotNull($book->getCoverPath());

        self::assertFileExists(__DIR__ . '/uploadTest');
    }

    public function testShow(): void
    {
        $fixture = new Book();
        $fixture->setTitle('Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Title');
    }

    public function testEdit(): void
    {
        $fixture = new Book();
        $fixture->setTitle('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Editar', [
            'book[title]' => 'Something New',
        ]);

        self::assertResponseRedirects('/book');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
    }

    public function testRemove(): void
    {
        $fixture = new Book();
        $fixture->setTitle('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/book');
        self::assertSame(0, $this->repository->count([]));
    }
    protected function tearDown(): void
    {
        $file = new Filesystem;
        $file->remove(__DIR__ . '/uploadTest');
        parent::tearDown();
    }
}
