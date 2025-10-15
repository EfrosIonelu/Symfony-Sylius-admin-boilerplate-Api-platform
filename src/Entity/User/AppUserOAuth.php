<?php

namespace App\Entity\User;

use App\Entity\User\Interfaces\UserOAuthInterface;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\User\Model\UserOAuth as BaseUserOAuth;

#[ORM\Table(name: 'app_user_oauth')]
#[ORM\Entity]
#[ORM\AssociationOverrides([new ORM\AssociationOverride(name: 'user', joinColumns: new ORM\JoinColumn(name: 'user_id', onDelete: 'CASCADE'))])]
class AppUserOAuth extends BaseUserOAuth implements UserOAuthInterface
{
}
