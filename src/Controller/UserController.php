<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Contact;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\HttpFoundation\Request;


class UserController extends AbstractController
{
    //function qui permet de génerer le formulaire 
    function formulaire(Request $request, \Swift_Mailer $mailer)
    {        
        
        // on recupere les informations de contact stocké en session. 
        $infoUser = $this->getUser();
        
        // on crée le formulaire grace à l'entité crée. 
        $contact = new Contact();
        $form = $this->createFormBuilder($contact)
        ->add('first_name', TextType::class, [
            'label' => 'Prenom',
            'data' => $infoUser->getFirstName(),
            'constraints' => [ 
                new NotBlank([ 'message' => 'Votre prenom ne peut pas être vide']),
                new Length( [
                    'max' => 255,
                    'maxMessage' => 'Votre prénom ne peut contenir plus de 255 caractères'
                ])
            ]
        ])
            ->add('name', TextType::class,  [
                'label' => 'Nom',
                'data' => $infoUser->getName(),
                'constraints' => [
                    new NotBlank([ 'message' => 'Votre nom ne doit pas être vide']),
                    new Length( [
                        'max' => 255,
                        'maxMessage' => 'Votre nom ne peut contenir plus de 255 caractères'
                    ])
                ]
            ])
        ->add('phone_number', TextType::class, [
            'label' => 'Numéro de téléphone',
            'constraints' => [
                new Length( [
                    'min' => 10,
                    'max' => 10,
                    'maxMessage' => 'Votre numéro de téléphone doit comporter 10 chiffres',
                    'minMessage' => 'Votre numéro de téléphone doit comporter 10 chiffres'
                    
                ])
            ]
        ])
        ->add('email', TextType::class, [
            'label' => 'Courriel',
            'data' => $infoUser->getEmail(),
            'constraints' => [
                new NotBlank([ 'message' => 'Vous email ne peut pas être vide']),
                new Email(['message' => 'L\'adresse email saisie est invalide'])
            ]
        ])
        ->add('message', TextareaType::class, [
            'constraints' => [
                new NotBlank([ 'message' => 'Votre message ne peux pas être vide.'])
            ]
        ])
        ->add('Envoyer', SubmitType::class)
        ->getForm();
        
        // on recupère les informations envoyés au formulaire
        $form->handleRequest($request);
        // on verifie que ça a été envoyé et que le formulaire soit valide. 
        if ($form->isSubmitted() && $form->isValid())
        {
            // recupération des informations dans la balise infoForm
            $infoForm = $form->getData(); 
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();
            // enregistrement en BDD 
            
            // envoi du mail 
            
            $courriel = (new \Swift_Message('Formulaire de contact'))
            ->setFrom('send@example.com')
            ->setTo($infoForm->getEmail())
            ->setBody(
                $this->renderView(
                    'contact.html.twig',
                    ['infoEmail' => $infoForm]
                    ),
                'text/html'
                );
            
            $mailer->send($courriel);
            
            
            // on redirige vers la page de formulaire. 
            return $this->redirectToRoute('formulaire', ['succes' => 'yes']);
         
        }
        
        return $this->render('formulaire.html.twig', ['form' => $form->createView()]);
           
    }
}

