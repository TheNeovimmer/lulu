<?php
$pageTitle = $title ?? 'Support CTT';
$sidebarLinks = [
  ['url' => '/ctt/dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
  ['url' => '/ctt/tickets', 'icon' => 'bi-ticket', 'label' => 'Gestion Tickets'],
  ['url' => '/ctt/tickets?type=maman', 'icon' => 'bi-heart', 'label' => 'Support Mamans'],
  ['url' => '/ctt/tickets?type=expert', 'icon' => 'bi-person-badge', 'label' => 'Support Experts'],
  ['url' => '/ctt/faq', 'icon' => 'bi-question-circle', 'label' => 'FAQ'],
  ['url' => '/ctt/historique', 'icon' => 'bi-clock-history', 'label' => 'Historique'],
  ['url' => '/ctt/rapports', 'icon' => 'bi-bar-chart', 'label' => 'Rapports'],
  ['url' => '/ctt/notifications', 'icon' => 'bi-bell', 'label' => 'Notifications'],
];
require __DIR__ . '/dashboard.php';
