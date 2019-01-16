<?php
namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\User;
use AppBundle\Entity\Post;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ConsumerProfileForm extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$user = $options['user'];
		$userChannels = $user->getChannels();

		$builder->add('search', TextType::class, [
		    'required' => false,
		    'translation_domain' => 'UserBundle',
		])
		->add('channels', EntityType::class,[
			'required' => false,
			'expanded' => false,
			'multiple' => true,
			'class' => 'AppBundle:Channel',
			'choice_label' => 'name',
			'query_builder' => function (EntityRepository $er) use ($userChannels) {
				return $er->createQueryBuilder('c')
				          ->Where('c.id IN (:ids)')
				          ->setParameter('ids', $userChannels)
				          ->orderBy('c.name', 'ASC');
			},
		])
		->add('tags', TextType::class, array(
		    'required' => false,
		    'translation_domain' => 'UserBundle',
		))
		->add('tags_ids', HiddenType::class, array())
		->add('notes', CheckboxType::class, array(
		    'required' => false,
		    'label' => 'consumer.filter.note',
		    'translation_domain' => 'UserBundle',
		));
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setRequired('user');
		$resolver->setDefaults(array(
			'csrf_protection' => false,
		));
	}

	public function getName()
	{
		return 'consumer_profile_form';
	}
}