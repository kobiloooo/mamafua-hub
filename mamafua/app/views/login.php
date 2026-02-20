<section class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4 mb-3">Welcome back</h1>
                <form id="loginForm">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <div class="mb-3"><label>Email</label><input class="form-control" type="email" name="email" required></div>
                    <div class="mb-3"><label>Password</label><input class="form-control" type="password" name="password" required minlength="8"></div>
                    <button class="btn btn-brand w-100" type="submit">Login</button>
                </form>
            </div>
        </div>
    </div>
</section>
