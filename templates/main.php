<main class="container">
  <section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
    <ul class="promo__list">
      <!--заполните этот список из массива категорий-->
      <?php foreach ($categories as $item) : ?>
      <li class="promo__item promo__item--<?= $item["character_code"] ?? "" ?>">
        <a class="promo__link" href="/all-lots.php?id=<?= $item["id"] ?? "" ?>"><?= $item["name"] ?? "" ?></a>
      </li>
      <?php endforeach; ?>
    </ul>
  </section>
  <section class="lots">
    <div class="lots__header">
      <h2>Открытые лоты</h2>
    </div>
    <ul class="lots__list">
      <!--заполните этот список из массива с товарами-->
      <?php foreach ($announcements as $announcement) : ?>
      <li class="lots__item lot">
        <div class="lot__image">
          <img src="<?= $announcement["image"] ?? "" ?>" width="350" height="260" alt="<?= clearStrDataTags($announcement["name"] ?? "") ?>">
        </div>
        <div class="lot__info">
          <span class="lot__category"><?= $announcement["category"] ?? "" ?></span>
          <h3 class="lot__title"><a class="text-link" href="/lot.php?id=<?= $announcement["id"] ?? "" ?>"><?= clearStrDataTags($announcement["name"] ?? "") ?></a></h3>
          <div class="lot__state">
            <div class="lot__rate">
              <span class="lot__amount">Стартовая цена</span>
              <span class="lot__cost"><?= isset($announcement["start_price"]) ? formatPrice($announcement["start_price"]) : "" ?></span>
            </div>
            <?php $time = isset($announcement["date_completion"]) ? calcDateExpiration($announcement["date_completion"]) : [] ?>
            <div class="lot__timer timer <?= isset($time["hours"]) && ($time["hours"] < 1) ? "timer--finishing" : "" ?>">
              <?= count($time) > 0 ? "{$time["hours"]}:{$time["minutes"]}:{$time["seconds"]}" : "н.д" ?>
            </div>
          </div>
        </div>
      </li>
      <?php endforeach; ?>
    </ul>
  </section>
</main>