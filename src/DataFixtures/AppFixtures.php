<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private $faker;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->faker = Factory::create();
    }

    private $encoder;


    public function load(ObjectManager $manager)
    {

        $this->loadUsers($manager);
        $users = $manager->getRepository(User::class)->findAll();
        $this->loadPosts($manager, $users);
        $questions = $manager->getRepository(Question::class)->findAll();
        $this->loadPostsComments($manager, $users, $questions);
    }

    public function loadUsers($manager)
    {
        for ($i = 1; $i < 10; $i++) {
            $user = new User();
            $user->setUsername($this->faker->userName);

            $password = $this->encoder->encodePassword($user, 'pass_1234');
            $user->setPassword($password);
            $user->setEnabled(true);
            $user->setEmail($this->faker->email);
            $manager->persist($user);
        }
        $manager->flush();
    }

    public function loadPosts($manager, $users)
    {
        for ($i = 1; $i < 51; $i++) {
            $user = $users[rand(0,1)];
            $question = new Question();
            $question->setQuestion($this->faker->text(30));
            $question->setBody($this->faker->text(100));
            $question->setCreated($this->faker->dateTime);
            $question->setEmail($user->getEmail());
            $question->setUsername($user->getUsername());
            $question->setUser($user);
            $question->setPicture('fixtures/'.rand(1, 8).'.jpg');
            $question->setValidate(true);
            $manager->persist($question);
        }
        $manager->flush();
    }

    public function loadPostsComments($manager, $users, $questions)
    {
        for ($i = 1; $i < 20; $i++) {
            $user = $users[rand(0,1)];
            $question= $questions[rand(0,1)];
            $answer = new Answer();
            $answer->setBody($this->faker->text(40));
            $answer->setUser($user);
            $answer->setQuestion($question);
            $manager->persist($answer);
        }
        $manager->flush();
    }
}
