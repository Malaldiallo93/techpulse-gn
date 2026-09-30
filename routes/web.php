<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EcosystemController;
use App\Http\Controllers\GlossaryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\OfflineController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

// ---------- Lecteur ----------
Route::get('/', HomeController::class)->name('home');

Route::get('/actualites', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/actualites/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');

Route::get('/opportunites', [OpportunityController::class, 'index'])->name('opportunities.index');
Route::get('/opportunites/{opportunity:slug}', [OpportunityController::class, 'show'])->name('opportunities.show');
Route::post('/opportunites/{opportunity:slug}/rappel', [OpportunityController::class, 'remind'])->name('opportunities.remind')->middleware('throttle:30,1');

Route::get('/apprendre', [CourseController::class, 'index'])->name('courses.index');
Route::get('/apprendre/{course:slug}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/apprendre/{course:slug}/lecon-{position}', [CourseController::class, 'lesson'])->whereNumber('position')->name('courses.lesson');
Route::post('/apprendre/{course:slug}/progression', [CourseController::class, 'progress'])->name('courses.progress')->middleware('throttle:60,1');

Route::get('/glossaire', [GlossaryController::class, 'index'])->name('glossary');
Route::get('/glossaire.json', [GlossaryController::class, 'data'])->name('glossary.json');

Route::get('/ecosysteme', [EcosystemController::class, 'index'])->name('ecosystem');
Route::post('/ecosysteme/evenements/{event}/participer', [EcosystemController::class, 'attend'])->name('events.attend')->middleware('throttle:30,1');

Route::get('/recherche', [SearchController::class, 'index'])->name('search');
Route::get('/recherche/index.json', [SearchController::class, 'data'])->name('search.json');

Route::get('/a-propos', [AboutController::class, 'index'])->name('about');
Route::post('/contribuer', [AboutController::class, 'contribute'])->name('contribute')->middleware('throttle:10,1');
Route::post('/suggestions', [AboutController::class, 'suggest'])->name('suggest')->middleware('throttle:10,1');
Route::view('/mentions-legales', 'pages.legal')->name('legal');

Route::get('/hors-ligne', OfflineController::class)->name('offline');

Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter')->middleware('throttle:10,1');
Route::get('/newsletter/desinscription/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// ---------- Back-office éditeur ----------
Route::get('/redaction/connexion', [AuthController::class, 'form'])->name('login');
Route::post('/redaction/connexion', [AuthController::class, 'login'])->middleware('throttle:6,1');
Route::post('/redaction/deconnexion', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->prefix('redaction')->name('redaction.')->group(function () {
    Route::get('/', [ReviewController::class, 'index'])->name('index');
    Route::get('/{article:id}', [ReviewController::class, 'show'])->name('show');
    Route::post('/{article:id}/passages/{flag}', [ReviewController::class, 'flag'])->name('flag');
    Route::post('/{article:id}/source', [ReviewController::class, 'source'])->name('source');
    Route::post('/{article:id}/niveau', [ReviewController::class, 'level'])->name('level');
    Route::post('/{article:id}/modifier', [ReviewController::class, 'edit'])->name('edit');
    Route::post('/{article:id}/publier', [ReviewController::class, 'publish'])->name('publish');
    Route::post('/{article:id}/rejeter', [ReviewController::class, 'reject'])->name('reject');
    Route::post('/{article:id}/rouvrir', [ReviewController::class, 'reopen'])->name('reopen');
});
