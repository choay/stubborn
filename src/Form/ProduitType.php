<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options): void
{
$builder
->add('nom')
->add('prix')
->add('imageFile', FileType::class, [
'label' => 'Image',
'required' => false,
'mapped' => false, // Not mapped to the entity field directly
])
->add('stockXS', IntegerType::class, [
'label' => 'Stock XS',
'mapped' => false,
'required' => false,
])
->add('stockS', IntegerType::class, [
'label' => 'Stock S',
'mapped' => false,
'required' => false,
])
->add('stockM', IntegerType::class, [
'label' => 'Stock M',
'mapped' => false,
'required' => false,
])
->add('stockL', IntegerType::class, [
'label' => 'Stock L',
'mapped' => false,
'required' => false,
])
->add('stockXL', IntegerType::class, [
'label' => 'Stock XL',
'mapped' => false,
'required' => false,
]);
}

public function configureOptions(OptionsResolver $resolver): void
{
$resolver->setDefaults([
'data_class' => Produit::class,
]);
}
}
