<?php
$pageTitle = $title ?? 'Dashboard Admin';
$sidebarLinks = [
  ['url' => '/admin', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
  ['url' => '/admin/articles', 'icon' => 'bi-file-text', 'label' => 'Articles'],
  ['url' => '/admin/categories', 'icon' => 'bi-tags', 'label' => 'Catégories'],
  ['url' => '/admin/utilisateurs', 'icon' => 'bi-people', 'label' => 'Utilisateurs'],
  ['url' => '/admin/ressources', 'icon' => 'bi-book', 'label' => 'Ressources'],
  ['url' => '/admin/communaute', 'icon' => 'bi-chat-dots', 'label' => 'Communauté'],
  ['url' => '/admin/tickets', 'icon' => 'bi-ticket', 'label' => 'Tickets'],
  ['url' => '/admin/comments', 'icon' => 'bi-chat-square-text', 'label' => 'Commentaires'],
  ['url' => '/admin/testimonials', 'icon' => 'bi-star', 'label' => 'Témoignages'],
  ['url' => '/admin/faqs', 'icon' => 'bi-question-circle', 'label' => 'FAQ'],
  ['url' => '/admin/contacts', 'icon' => 'bi-envelope', 'label' => 'Messages'],
  ['url' => '/admin/newsletters', 'icon' => 'bi-mailbox', 'label' => 'Newsletter'],
  ['url' => '/admin/parametres', 'icon' => 'bi-gear', 'label' => 'Paramètres'],
];
require __DIR__ . '/dashboard.php';
