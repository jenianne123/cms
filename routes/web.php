<?php

use App\Http\Controllers\FaqController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\GenericController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\QuickLinksController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return redirect()->route('login'); 
});

Route::get('/login', function () {
    return Inertia::render('Login');  
})->name('login');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/genericpages', function () {
    return Inertia::render('GenericPages');
})->middleware(['auth', 'verified'])->name('genericpages');

Route::get('/articlepage', function () {
    return Inertia::render('ArticlePage');
})->middleware(['auth', 'verified'])->name('articlepage');

Route::get('/quickLink', function () {
    return Inertia::render('QuickLink');
})->middleware(['auth', 'verified'])->name('quickLink');

Route::get('/image', function () {
    return Inertia::render('Image');
})->middleware(['auth', 'verified'])->name('image');

Route::get('/resetPassword', function () {
    return Inertia::render('Reset Password');
})->middleware(['auth', 'verified'])->name('resetPassword');

Route::get('/ckeditor', function () {
    return Inertia::render('ArticlePage');
})->middleware(['auth', 'verified'])->name('ckeditor');

// faqs routes
Route::get('/faqs', function () {
    return Inertia::render('Faqs');
})->middleware(['auth', 'verified'])->name('faqs');
Route::get('/faqlist',[FaqController::class, 'table']);
Route::post('/delete-faqs',[FaqController::class, 'deleteFaqs']);
Route::post('/submit-form',[FaqController::class, 'saveFaq']);
Route::get('/faqs-data/{id?}',[FaqController::class, 'editForm']);
Route::post('/submit-edit-faq', [FaqController::class, 'editFaqSave']);


// account route
Route::get('/manageacc', function () {
    return Inertia::render('ManageAcc'); 
})->middleware(['auth', 'verified'])->name('manageacc');
Route::get('/userlist',[UserController::class, 'table']);
Route::post('/submit-user',[UserController::class, 'saveAccount']);
Route::get('/user-data/{id?}',[UserController::class, 'editForm']);
Route::post('/submit-edit-form',[UserController::class, 'editFormSave']);
// Route::get('/get-user-status/{id}', [UserController::class, 'getUserStatus']);
Route::post('/deactivate-acc/{id}', [UserController::class, 'deactivateAcc']);
Route::post('/activate-acc/{id}', [UserController::class, 'activateAcc']);
Route::get('/check-user-exists/{email}', [UserController::class, 'checkUserExists']);
Route::get('/changepassword', function () {
    return Inertia::render('ChangePassword');
})->middleware(['auth', 'verified'])->name('changepassword');
Route::get('/get-user-access', [UserController::class, 'getUserAccess']);
Route::post('/submit-user-access', [UserController::class, 'editUserAccess']);


//for generic pages
Route::get('/genericlist',[GenericController::class, 'table']);
Route::post('/delete-content',[GenericController::class, 'deleteContent']);
Route::get('/content-data/{id?}',[GenericController::class, 'editForm']);
Route::post('/submit-page',[GenericController::class, 'savePage']);
Route::get('/get-parent/{id?}',[GenericController::class, 'getParent']);
Route::get('/content-data/{id?}', [GenericController::class, 'editForm']);
Route::post('/submit-edit-page', [GenericController::class, 'editFormSave']);



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard}', [UserController::class, 'dashboard']);
});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified'])->group(function () {

Route::get('/images', [ImageController::class, 'index']);
Route::post('/uploadimage', [ImageController::class, 'saveDoc']);
});

// activate or deactivate route
Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        // Authentication passed...

        // Check user status before proceeding
        $user = Auth::user();

        if ($user->status == 1) {
            return redirect()->intended('/dashboard'); // Redirect to the dashboard on successful login
        } else {
            Auth::logout();
            return redirect()->route('login')->with('status', 'Your account is not active.');
        }
    }

    return redirect()->route('login')->with('status', 'Invalid login credentials.');
});


// For images
Route::get('/images', [ImageController::class, 'index']);
Route::post('/uploadimage', [ImageController::class, 'saveDoc']);
Route::get('/imagelist', [ImageController::class, 'tableImages']);
Route::delete('/delete-image/{id}', [ImageController::class, 'deleteImage']);
Route::post('/submit-password', [UserController::class, 'editPassword']);

//for generic pages
Route::get('/genericlist',[GenericController::class, 'table']);
Route::post('/delete-page',[GenericController::class, 'deleteContent']);
Route::get('/content-data/{id?}',[GenericController::class, 'editForm']);
Route::post('/submit-page',[GenericController::class, 'savePage']);
Route::get('/get-parent/{id?}',[GenericController::class, 'getParent']);

//for articles
Route::get('/articlelist',[ArticleController::class, 'tableArticle']);
Route::post('/delete-article',[ArticleController::class, 'deleteArticle']);
Route::get('/article-data/{id?}',[ArticleController::class, 'editForm']);
Route::post('/submit-article',[ArticleController::class, 'saveArticle']);
Route::post('/upload-image',[ArticleController::class, 'saveImage']);
Route::get('/add-article', function () {
    return Inertia\Inertia::render('AddArticle');
});

//for quicklinks
Route::post('/submit-current',[QuickLinksController::class, 'saveCurAware']);
Route::get('/get-parent/{section}/{linkCategory?}', [QuickLinksController::class, 'getParent']);
// Route::get('/get-parent/{id?}',[QuickLinksController::class, 'getParent']);
Route::get('/quicklinklist',[QuickLinksController::class, 'table']);
Route::post('/delete-content', [QuickLinksController::class, 'deleteContent']);
// Route to get a specific QuickLink for editing
Route::get('/get-quicklink/{id}', [QuickLinksController::class, 'getQuickLink']);
// Route to update an existing QuickLink
Route::post('/edit-quicklink/{id}', [QuickLinksController::class, 'editQuickLink']);



