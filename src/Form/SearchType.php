<?php
namespace App\Form;

use App\Data\SearchData;
use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('searchPrix', ChoiceType::class, [
                'choices' => [
                    '10 à 29 €' => '10-29',
                    '29 à 35 €' => '29-35',
                    '35 à 50 €' => '35-50',
                ],
                'label' => 'Filtrer par prix',
                'required' => false,
                'placeholder' => 'Choisir une fourchette',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'csrf_protection' => false,
            'method' => 'GET',
        ]);
    }
    public function getBlockPrefix()
    {
        return '';
    }
}