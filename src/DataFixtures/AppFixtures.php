<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $blogPost = new BlogPost();
        $blogPost->setTitle('A first post!');
        $blogPost->setPublished(new \DateTime('2022-02-15T12:00:00+00:00'));
        $blogPost->setContent('Post text');
        $blogPost->setAuthor('Przemek');
        $blogPost->setSlug('a-first-post');

        $manager->persist($blogPost);

        $blogPost = new BlogPost();
        $blogPost->setTitle('A second post!');
        $blogPost->setPublished(new \DateTime('2022-02-15T12:00:00+00:00'));
        $blogPost->setContent('Post text');
        $blogPost->setAuthor('Przemek');
        $blogPost->setSlug('a-second-post');

        $manager->persist($blogPost);

        $manager->flush();
    }
}
