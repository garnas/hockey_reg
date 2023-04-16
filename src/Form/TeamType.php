<?php

namespace App\Form;

use App\Entity\Team;
use App\Util\Util;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamType extends AbstractType
{

    public static function addCommonFields(FormBuilderInterface $builder): void
    {
        $builder
            ->add('teamname')
            ->add('email', EmailType::class, [
                'attr' => ['autocomplete' => 'email']
            ])
            ->add('country', ChoiceType::class, [
                'choices' => Util::getCountries(),
                'placeholder' => 'Select ...',
                'help' => 'Select the country for which your team plays. You can also choose International.',
            ])
            ->add('tournamentLevel', ChoiceType::class, [
                'placeholder' => 'Select ...',
                'help' => 'Select the tournament in which your team prefers to participate.',
                'choices' => [
                    "A-Tournament" => "A",
                    "B-Tournament" => "B",
                    "Undecided" => "Undecided",
                ]
            ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        self::addCommonFields($builder);
//        $builder->add('additionalEmails', TextType::class, [
//            'help' => 'Insert comma separated email addresses.',
//        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }
}
