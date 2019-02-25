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
        
        // on recupere les informations de contact 
        $infoUser = $this->getUser();
        
        
        $contact = new Contact();
        $form = $this->createFormBuilder($contact)
        ->add('first_name', TextType::class, [
            'label' => 'Prenom',
            'data' => $infoUser->getFirstName(),
            'constraints' => [ 
                new NotBlank([ 'message' => 'Vous devez reserver au moins une place']),
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
                    new NotBlank([ 'message' => 'Vous devez reserver au moins une place']),
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
                new NotBlank([ 'message' => 'Vous devez reserver au moins une place']),
                new Email(['message' => 'L\'adresse email saisie est invalide'])
            ]
        ])
        ->add('message', TextareaType::class, [
            'constraints' => [
                new NotBlank([ 'message' => 'Vous devez reserver au moins une place'])
            ]
        ])
        ->add('Envoyer', SubmitType::class)
        ->getForm();
        
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $infoForm = $form->getData(); 
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();
            
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
            
            

            return $this->redirectToRoute('formulaire');
         
        }
        
        return $this->render('formulaire.html.twig', ['form' => $form->createView()]);
           
    }
}

