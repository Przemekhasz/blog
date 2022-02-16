<?php

namespace App\Controller\Admin;

use App\Entity\BlogPost;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\DateTime;

class BlogPostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BlogPost::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Post')
            ->setEntityLabelInPlural('Posts')
            ->setSearchFields(['id']);
    }

    public function configureFields(string $pageName): iterable
    {
        $id       = IntegerField::new('id', 'ID');
        $title     = TextField::new('title');
        $published = DateTimeField::new('datetime');
        $content = TextField::new('content');
        $author = TextField::new('author');
        $slug = TextField::new('slug');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $title, $published, $content, $author, $slug];
        }

        return [
            FormField::addPanel('Post'),
            $title, $published, $content, $author, $slug
        ];
    }

}
