<?php
$router->get('', 'PageController@home');
$router->get('a-propos', 'PageController@about');
$router->get('blog', 'ArticleController@index');
$router->get('blog/{slug}', 'ArticleController@show');
$router->post('blog/{slug}/comment', 'ArticleController@comment');
$router->get('ressources', 'ResourceController@index');
$router->get('ressources/{slug}', 'ResourceController@show');
$router->get('communaute', 'CommunityController@index');
$router->get('communaute/{id}', 'CommunityController@show');
$router->post('communaute/{id}/comment', 'CommunityController@comment');
$router->post('communaute/{id}/like', 'CommunityController@like');
$router->get('contact', 'ContactController@index');
$router->post('contact', 'ContactController@store');
$router->get('faq', 'FaqController@index');
$router->get('auth/login', 'AuthController@login');
$router->post('auth/login', 'AuthController@authenticate');
$router->get('auth/register', 'AuthController@register');
$router->post('auth/register', 'AuthController@store');
$router->get('auth/logout', 'AuthController@logout');

// Maman dashboard
$router->get('dashboard', 'DashboardController@index');
$router->post('dashboard', 'DashboardController@index');
$router->get('dashboard/profil', 'DashboardController@profil');
$router->post('dashboard/profil', 'DashboardController@updateProfil');
$router->get('dashboard/grossesse', 'DashboardController@grossesse');
$router->post('dashboard/grossesse', 'DashboardController@updateGrossesse');
$router->get('dashboard/bebe', 'DashboardController@bebe');
$router->post('dashboard/bebe', 'DashboardController@updateBebe');
$router->get('dashboard/croissance', 'DashboardController@croissance');
$router->post('dashboard/croissance', 'DashboardController@addCroissance');
$router->get('dashboard/vaccination', 'DashboardController@vaccination');
$router->post('dashboard/vaccination', 'DashboardController@addVaccination');
$router->get('dashboard/tickets', 'DashboardController@tickets');
$router->post('dashboard/tickets', 'DashboardController@createTicket');
$router->get('dashboard/notifications', 'DashboardController@notifications');
$router->post('dashboard/notifications/read-all', 'DashboardController@readAllNotifications');
$router->post('dashboard/notifications/read/{id}', 'DashboardController@readNotification');
$router->get('dashboard/parametres', 'DashboardController@parametres');
$router->post('dashboard/parametres', 'DashboardController@updateParametres');
$router->post('dashboard/bebe/memories', 'DashboardController@memories');
$router->post('dashboard/bebe/memories/delete/{id}', 'DashboardController@deleteMemory');
$router->post('dashboard/bebe/milestones', 'DashboardController@updateMilestones');
$router->get('dashboard/rendez-vous', 'DashboardController@appointments');
$router->post('dashboard/rendez-vous/book', 'DashboardController@bookAppointment');
$router->get('dashboard/messagerie', 'DashboardController@messages');
$router->post('dashboard/messagerie/send', 'DashboardController@sendMessage');
$router->get('dashboard/agenda', 'DashboardController@agenda');

// Expert directory routes
$router->get('experts', 'ExpertController@directory');
$router->get('experts/{id}', 'ExpertController@showProfile');

// Expert parameters route
$router->get('expert/parametres', 'ExpertController@parametres');
$router->post('expert/parametres', 'ExpertController@updateParametres');

// Expert dashboard
$router->get('expert/dashboard', 'ExpertController@index');
$router->get('expert/profil', 'ExpertController@profil');
$router->post('expert/profil', 'ExpertController@updateProfil');
$router->get('expert/questions', 'ExpertController@questions');
$router->post('expert/questions/{id}', 'ExpertController@answerQuestion');
$router->get('expert/articles', 'ExpertController@articles');
$router->post('expert/articles/create', 'ExpertController@createArticle');
$router->get('expert/ressources', 'ExpertController@ressources');
$router->get('expert/notifications', 'ExpertController@notifications');

// CTT dashboard
$router->get('ctt/dashboard', 'CttController@index');
$router->get('ctt/tickets', 'CttController@tickets');
$router->post('ctt/tickets/update/{id}', 'CttController@updateTicket');
$router->post('ctt/tickets/assign/{id}', 'CttController@assignTicket');
$router->get('ctt/faq', 'CttController@faq');
$router->post('ctt/faq/create', 'CttController@createFaq');
$router->get('ctt/historique', 'CttController@historique');
$router->get('ctt/rapports', 'CttController@rapports');
$router->get('ctt/notifications', 'CttController@notifications');

// Admin routes
$router->get('admin', 'AdminController@index');
$router->get('admin/articles', 'AdminArticleController@index');
$router->get('admin/articles/create', 'AdminArticleController@create');
$router->post('admin/articles/create', 'AdminArticleController@store');
$router->get('admin/articles/edit/{id}', 'AdminArticleController@edit');
$router->post('admin/articles/edit/{id}', 'AdminArticleController@update');
$router->post('admin/articles/delete/{id}', 'AdminArticleController@destroy');
$router->get('admin/categories', 'AdminCategoryController@index');
$router->post('admin/categories/create', 'AdminCategoryController@store');
$router->post('admin/categories/delete/{id}', 'AdminCategoryController@destroy');
$router->get('admin/utilisateurs', 'AdminUserController@index');
$router->post('admin/utilisateurs/toggle-role/{id}', 'AdminUserController@toggleRole');
$router->post('admin/utilisateurs/suspendre/{id}', 'AdminUserController@suspend');
$router->post('admin/utilisateurs/delete/{id}', 'AdminUserController@destroy');
$router->get('admin/mamans', 'AdminMotherController@index');
$router->get('admin/experts', 'AdminExpertController@index');
$router->post('admin/experts/validate/{id}', 'AdminExpertController@validate');
$router->get('admin/ressources', 'AdminResourceController@index');
$router->get('admin/ressources/create', 'AdminResourceController@create');
$router->post('admin/ressources/create', 'AdminResourceController@store');
$router->post('admin/ressources/delete/{id}', 'AdminResourceController@destroy');
$router->get('admin/communaute', 'AdminCommunityController@index');
$router->post('admin/communaute/hide/{id}', 'AdminCommunityController@hide');
$router->post('admin/communaute/delete/{id}', 'AdminCommunityController@destroy');
$router->get('admin/tickets', 'AdminTicketController@index');
$router->post('admin/tickets/assign/{id}', 'AdminTicketController@assign');
$router->post('admin/tickets/close/{id}', 'AdminTicketController@close');
$router->get('admin/comments', 'AdminCommentController@index');
$router->post('admin/comments/approve/{id}', 'AdminCommentController@approve');
$router->post('admin/comments/reject/{id}', 'AdminCommentController@reject');
$router->get('admin/testimonials', 'AdminTestimonialController@index');
$router->post('admin/testimonials/approve/{id}', 'AdminTestimonialController@approve');
$router->post('admin/testimonials/reject/{id}', 'AdminTestimonialController@reject');
$router->get('admin/faqs', 'AdminFaqController@index');
$router->post('admin/faqs/create', 'AdminFaqController@store');
$router->post('admin/faqs/delete/{id}', 'AdminFaqController@destroy');
$router->get('admin/contacts', 'AdminContactController@index');
$router->post('admin/contacts/mark-read/{id}', 'AdminContactController@markRead');
$router->post('admin/contacts/delete/{id}', 'AdminContactController@destroy');
$router->get('admin/newsletters', 'AdminNewsletterController@index');
$router->post('admin/newsletters/delete/{id}', 'AdminNewsletterController@destroy');
$router->get('admin/parametres', 'AdminSettingsController@index');
$router->post('admin/parametres', 'AdminSettingsController@update');
