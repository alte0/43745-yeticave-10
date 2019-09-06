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
  <section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">

      <?php foreach ($myBets as $item) : ?>
        <?php
          $time = calcDateExpiration($item["date_completion"]);
          $url = "/lot.php?id={$item["id"]}";
          $name = $item["name"];
          $isTimeEnd = (int)$time["hours"] === 0 && (int)$time["minutes"] === 0 && (int)$time["seconds"] === 0;
          ?>
        <tr class="rates__item <?= $isTimeEnd ? "rates__item--end" : "" ?>">
          <td class="rates__info">
            <div class="rates__img">
              <img src="../<?= $item["image"] ?>" width="54" height="40" alt="Сноуборд">
            </div>
            <?php if (!empty($item["contacts"])) : ?>
              <div>
                <h3 class="rates__title"><a href="<?= $url ?>"><?= $name ?></a></h3>
                <p><?= clearStrDataTags($item["contacts"]) ?></p>
              </div>
            <?php else : ?>
              <h3 class="rates__title"><a href="<?= $url ?>"><?= $name ?></a></h3>
            <?php endif; ?>
          </td>
          <td class="rates__category">
            <?= $item["category_name"] ?>
          </td>
          <td class="rates__timer">
            <div class="timer <?= $time["hours"] < 1 && !$isTimeEnd ? "timer--finishing" : "" ?><?= $isTimeEnd ? "timer--end" : "" ?>">
              <?= $isTimeEnd ? "Торги окончены" : "{$time["hours"]}:{$time["minutes"]}:{$time["seconds"]}" ?>
            </div>
          </td>
          <td class="rates__price">
            <?= formatPrice($item["bet_price"], false) ?> р
          </td>
          <td class="rates__time">
            <?= getAgoText($today, $item["bet_date_create"]) ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </section>
</main>