<main>
  <?= $categoriesNav ?>
  <form class="form container <?= !empty($errors) ? "form--invalid" : "" ?>" action="/sign-up.php" method="post" autocomplete="off">
    <!-- form
  --invalid -->
    <h2>Регистрация нового аккаунта</h2>
    <div class="form__item <?= isset($errors["email"]) ? "form__item--invalid" : "" ?>">
      <!-- form__item--invalid -->
      <label for="email">E-mail <sup>*</sup></label>
      <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= getPostVal("email") ?>">
      <span class="form__error">Введите e-mail<?= isset($errors["email"]) ? addCommaAndSpaceText($errors["email"]) : "" ?></span>
    </div>
    <div class="form__item <?= isset($errors["password"]) ? "form__item--invalid" : "" ?>">
      <label for="password">Пароль <sup>*</sup></label>
      <input id="password" type="password" name="password" placeholder="Введите пароль" value="<?= getPostVal("password") ?>">
      <span class="form__error">Введите пароль<?= isset($errors["password"]) ? addCommaAndSpaceText($errors["password"]) : "" ?></span>
    </div>
    <div class="form__item <?= isset($errors["name"]) ? "form__item--invalid" : "" ?>">
      <label for="name">Имя <sup>*</sup></label>
      <input id="name" type="text" name="name" placeholder="Введите имя" value="<?= getPostVal("name") ?>">
      <span class=" form__error">Введите имя<?= isset($errors["name"]) ? addCommaAndSpaceText($errors["name"]) : "" ?></span>
    </div>
    <div class="form__item <?= isset($errors["message"]) ? "form__item--invalid" : "" ?>">
      <label for="message">Контактные данные <sup>*</sup></label>
      <textarea id="message" name="message" placeholder="Напишите как с вами связаться"><?= getPostVal("message") ?></textarea>
      <span class="form__error">Напишите как с вами связаться<?= isset($errors["message"]) ? addCommaAndSpaceText($errors["message"]) : "" ?></span>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Зарегистрироваться</button>
    <a class="text-link" href="/login.php">Уже есть аккаунт</a>
  </form>
</main>