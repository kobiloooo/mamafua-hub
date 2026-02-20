<section class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4 mb-3">Create your Mamafua account</h1>
                <form id="registerForm">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <div class="row g-3">
                        <div class="col-md-6"><label>Full Name</label><input class="form-control" name="name" required></div>
                        <div class="col-md-6"><label>Email</label><input class="form-control" type="email" name="email" required></div>
                        <div class="col-md-6"><label>Phone (2547XXXXXXXX)</label><input class="form-control" name="phone" pattern="2547[0-9]{8}" required></div>
                        <div class="col-md-6"><label>Password</label><input class="form-control" type="password" name="password" minlength="8" required></div>
                        <div class="col-md-12"><label>Role</label>
                            <select class="form-select" name="role" id="roleSelect">
                                <option value="client">Client (Household)</option>
                                <option value="mamafua">Mamafua (Housekeeper)</option>
                            </select>
                        </div>
                    </div>
                    <div id="mamafuaFields" class="row g-3 mt-1 d-none">
                        <div class="col-md-6"><label>Skills</label><div class="form-check"><input class="form-check-input" type="checkbox" name="skills[]" value="cleaning"> Cleaning</div><div class="form-check"><input class="form-check-input" type="checkbox" name="skills[]" value="laundry"> Laundry</div><div class="form-check"><input class="form-check-input" type="checkbox" name="skills[]" value="childcare"> Childcare</div><div class="form-check"><input class="form-check-input" type="checkbox" name="skills[]" value="cooking"> Cooking</div></div>
                        <div class="col-md-6"><label>Location</label><input class="form-control" name="location" value="Nairobi"></div>
                        <div class="col-md-12"><label>Availability</label><input class="form-control" name="availability" value="Weekdays 8am-5pm"></div>
                    </div>
                    <button class="btn btn-brand mt-3 w-100" type="submit">Register</button>
                </form>
            </div>
        </div>
    </div>
</section>
