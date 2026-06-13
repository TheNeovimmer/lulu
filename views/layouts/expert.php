<?php
$pageTitle = $title ?? 'Espace Expert';
$sidebarLinks = [
  ['url' => '/expert/dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
  ['url' => '/expert/profil', 'icon' => 'bi-file-earmark-person', 'label' => 'Profil Professionnel'],
  ['url' => '/expert/questions', 'icon' => 'bi-question-circle', 'label' => 'Questions Mamans'],
  ['url' => '/expert/articles', 'icon' => 'bi-file-text', 'label' => 'Articles'],
  ['url' => '/expert/ressources', 'icon' => 'bi-book', 'label' => 'Ressources'],
  ['url' => '/expert/agenda', 'icon' => 'bi-calendar-week', 'label' => 'Mon Agenda'],
  ['url' => '/communaute', 'icon' => 'bi-chat-dots', 'label' => 'Communauté'],
  ['url' => '/expert/notifications', 'icon' => 'bi-bell', 'label' => 'Notifications'],
  ['url' => '/expert/parametres', 'icon' => 'bi-gear', 'label' => 'Paramètres'],
];
require __DIR__ . '/dashboard.php';
