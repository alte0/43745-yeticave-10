<main>
  <?= $categoriesNav ?>
  <section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">

      <?php foreach ($myBets as $item) : ?>
        <?php
          $time = isset($item["date_completion"]) ? calcDateExpiration($item["date_completion"]) : [];
          $isTimeEnd = (int) $time["hours"] === 0 && (int) $time["minutes"] === 0 && (int) $time["seconds"] === 0;
          $isWinner = (int)$item["user_id_winner"] === $userID ? true : false;
          ?>
        <tr class="rates__item <?= $isTimeEnd && !$isWinner ? "rates__item--end" : "" ?> <?= $isWinner ? "rates__item--win" : "" ?>">
          <td class="rates__info">
            <div class="rates__img">
              <img src="../<?= $item["image"] ?? "" ?>" width="54" height="40" alt="<?= clearStrDataTags($item["name"] ?? "") ?>">
            </div>
            <?php if (!empty($item["contacts"]) && $isWinner) : ?>
              <div>
                <h3 class="rates__title"><a href="/lot.php?id=<?= $item["id"] ?? "" ?>"><?= clearStrDataTags($item["name"] ?? "") ?></a></h3>
                <p><?= clearStrDataTags($item["contacts"] ?? "") ?></p>
              </div>
            <?php else : ?>
              <h3 class="rates__title"><a href="/lot.php?id=<?= $item["id"] ?? "" ?>"><?= clearStrDataTags($item["name"] ?? "") ?></a></h3>
            <?php endif; ?>
          </td>
          <td class="rates__category">
            <?= $item["category_name"] ?? "" ?>
          </td>
          <td class="rates__timer">
            <div class="timer <?= ($time["hours"] ?? "") < 1 && !$isTimeEnd ? "timer--finishing" : "" ?><?= $isTimeEnd && !$isWinner ? "timer--end" : "" ?> <?= $isWinner ? "timer--win" : "" ?>">
              <?= !$isTimeEnd && !$isWinner && count($time) > 0 ? "{$time["hours"]}:{$time["minutes"]}:{$time["seconds"]}" : "" ?>
              <?= $isTimeEnd && !$isWinner ? "Торги окончены" : "" ?>
              <?= $isWinner ? "Ставка выиграла" : "" ?>
            </div>
          </td>
          <td class="rates__price">
            <?= formatPrice($item["bet_price"] ?? "", false) ?> р
          </td>
          <td class="rates__time">
            <?= getAgoText($today, $item["bet_date_create"] ?? "") ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </section>
</main>