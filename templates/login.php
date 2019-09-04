<main>
  <nav class="nav">
    <ul class="nav__list container">
      <?php foreach ($categories as $item) : ?>
        <li class="nav__item">
          <a href="pages/all-lots.html"><?= $item["name"] ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
  </nav>
  <form class="form container <?= !empty($errors) ? "form--invalid" : "" ?>" action="/login.php" method="post">
    <!-- form--invalid -->
    <h2>Вход</h2>
    <div class="form__item <?= isset($errors["email"]) || isset($errors["common"]) ? "form__item--invalid" : "" ?>">
      <!-- form__item--invalid -->
      <label for="email">E-mail <sup>*</sup></label>
      <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= getPostVal("email") ?>">
      <span class="form__error">Введите e-mail<?= !empty($errors["email"]) ? addCommaAndSpaceText($errors["email"]) : "" ?><?= !empty($errors["common"]) ? addCommaAndSpaceText($errors["common"]) : "" ?></span>
    </div>
    <div class="form__item form__item--last  <?= isset($errors["password"]) || isset($errors["common"]) ? "form__item--invalid" : "" ?>">
      <label for="password">Пароль <sup>*</sup></label>
      <input id="password" type="password" name="password" placeholder="Введите пароль" value="<?= getPostVal("password") ?>">
      <span class="form__error">Введите пароль<?= !empty($errors["password"]) ? addCommaAndSpaceText($errors["email"]) : "" ?><?= !empty($errors["common"]) ? addCommaAndSpaceText($errors["common"]) : "" ?></span>
    </div>
    <button type="submit" class="button">Войти</button>
  </form>
</main>