<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BookFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Création de quelques auteurs
        $authors = [];
        for ($i = 0; $i < 10; $i++) {
            $author = new Author();
            $author->setFirstName($faker->firstName);
            $author->setLastName($faker->lastName);
            $author->setBirthDate($faker->dateTimeBetween('-80 years', '-20 years'));
            $manager->persist($author);
            $authors[] = $author;
        }

        // Création de catégories
        $categories = [];
        $categoryNames = ['Fiction', 'Non-fiction', 'Science', 'Fantasy', 'History', 'Biography'];
        foreach ($categoryNames as $name) {
            $category = new Category();
            $category->setName($name);
            $category->setDescription($faker->sentence);
            $manager->persist($category);
            $categories[] = $category;
        }

        // Création des livres
        for ($i = 0; $i < 50; $i++) {
            $book = new Book();
            $book->setTitle($faker->sentence(3, true));
            $book->setDescription($faker->paragraph);
            $book->setPublishedAt($faker->dateTimeBetween('-10 years', 'now'));
            
            // Auteur aléatoire
            $book->setAuthor($faker->randomElement($authors));
            
            // Ajout de 1 à 3 catégories
            $randomCategories = $faker->randomElements($categories, rand(1, 3));
            foreach ($randomCategories as $category) {
                $book->addCategory($category);
            }

            $manager->persist($book);
        }

        $manager->flush();
    }
}
