<main class="container">
  <section class="lot-item container">
    <h2><?= clearStrDataTags($lot["name"] ?? "") ?></h2>
    <div class="lot-item__content">
      <div class="lot-item__left">
        <div class="lot-item__image">
          <img src="../<?= $lot["image"] ?? "" ?>" width="730" height="548" alt="<?= clearStrDataTags($lot["name"] ?? "") ?>">
        </div>
        <p class="lot-item__category">Категория: <span><?= $lot["category_name"] ?? "" ?></span></p>
        <p class="lot-item__description"><?= clearStrDataTags($lot["description"] ?? "") ?></p>
      </div>
      <div class="lot-item__right">
        <div class="lot-item__state">
          <?php $time = isset($lot["date_completion"]) ? calcDateExpiration($lot["date_completion"]) : [] ?>
          <div class="lot-item__timer timer <?= isset($time["hours"]) && ($time["hours"] < 1) ? "timer--finishing" : "" ?>">
            <?= count($time) > 0 ? "{$time["hours"]}:{$time["minutes"]}" : "н.д" ?>
          </div>
          <div class="lot-item__cost-state">
            <div class="lot-item__rate">
              <span class="lot-item__amount">Текущая цена</span>
              <span class="lot-item__cost"><?= isset($lot["price"]) ? formatPrice($lot["price"], false) : "" ?></span>
            </div>
            <div class="lot-item__min-cost">
              Мин. ставка <span><?= isset($lot["price"]) && isset($lot["step"]) ? formatPrice($lot["price"] + $lot["step"], false) : "" ?> р</span>
            </div>
          </div>
          <?php if ($isVisibleForm) : ?>
            <form class="lot-item__form" action="/lot.php?id=<?= $lot["id"] ?>" method="post" autocomplete="off">
              <p class="lot-item__form-item form__item <?= isset($errors) && !empty($errors) ? "form__item--invalid" : "" ?>">
                <label for="cost">Ваша ставка</label>
                <input id="cost" type="text" name="cost" placeholder="<?= isset($lot["price"]) && isset($lot["step"]) ? formatPrice($lot["price"] + $lot["step"], false) : "" ?>" value='<?= getPostVal("cost") ?>'>
                <span class="form__error"><?= $errors["cost"] ?? "" ?></span>
              </p>
              <button type="submit" class="button">Сделать ставку</button>
            </form>
          <?php endif; ?>
        </div>
        <div class="history">
          <h3>История ставок (<span><?= isset($bets) ? count($bets) : "" ?></span>)</h3>
          <table class="history__list">
            <?php foreach ($bets as $item) : ?>
              <tr class="history__item">
                <td class="history__name"><?= clearStrDataTags($item["name"] ?? "") ?></td>
                <td class="history__price"><?= clearStrDataTags($item["price"] ?? "") ?> р</td>
                <td class="history__time"><?= getAgoText($today, $item["date"] ?? "") ?></td>
              </tr>
            <?php endforeach; ?>
          </table>
        </div>
      </div>
    </div>
  </section>
</main>