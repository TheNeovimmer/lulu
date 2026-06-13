<?php
$pageTitle = $title ?? 'Mon Espace';
$sidebarLinks = [
  ['url' => '/dashboard', 'icon' => 'bi-house', 'label' => 'Accueil'],
  ['url' => '/dashboard/profil', 'icon' => 'bi-person', 'label' => 'Mon Profil'],
  ['url' => '/dashboard/grossesse', 'icon' => 'bi-flower1', 'label' => 'Ma Grossesse'],
  ['url' => '/dashboard/bebe', 'icon' => 'bi-emoji-smile', 'label' => 'Mon Bébé'],
  ['url' => '/dashboard/croissance', 'icon' => 'bi-graph-up', 'label' => 'Croissance'],
  ['url' => '/dashboard/vaccination', 'icon' => 'bi-shield-check', 'label' => 'Vaccination'],
  ['url' => '/dashboard/rendez-vous', 'icon' => 'bi-calendar-event', 'label' => 'Rendez-vous'],
  ['url' => '/dashboard/messagerie', 'icon' => 'bi-envelope', 'label' => 'Messagerie'],
  ['url' => '/dashboard/agenda', 'icon' => 'bi-calendar-week', 'label' => 'Mon Agenda'],
  ['url' => '/blog', 'icon' => 'bi-journal-text', 'label' => 'Blog'],
  ['url' => '/ressources', 'icon' => 'bi-book', 'label' => 'Ressources'],
  ['url' => '/communaute', 'icon' => 'bi-chat-dots', 'label' => 'Communauté'],
  ['url' => '/dashboard/tickets', 'icon' => 'bi-ticket', 'label' => 'Support'],
  ['url' => '/dashboard/notifications', 'icon' => 'bi-bell', 'label' => 'Notifications'],
  ['url' => '/dashboard/parametres', 'icon' => 'bi-gear', 'label' => 'Paramètres'],
];
require __DIR__ . '/dashboard.php';
