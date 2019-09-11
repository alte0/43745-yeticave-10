<main>
  <?= $categoriesNav ?>
  <div class="container">
    <section class="lots">
      </ul>
      <?php if (count($lots)) : ?>
        <h2>Все лоты в категории «<span><?= $categoryName ?></span>»</h2>
        <ul class="lots__list">
          <?php foreach ($lots as $item) : ?>
            <li class="lots__item lot">
              <div class="lot__image">
                <img src="../<?= $item["image"] ?>" width="350" height="260" alt="Сноуборд">
              </div>
              <div class="lot__info">
                <span class="lot__category"><?= $item["category"] ?></span>
                <h3 class="lot__title"><a class="text-link" href="/lot.php?id=<?= ($item["id"]) ?>"><?= clearStrDataTags($item["name"]) ?></a></h3>
                <div class="lot__state">
                  <div class="lot__rate">
                    <span class="lot__amount">Стартовая цена</span>
                    <span class="lot__cost"><?= formatPrice($item["start_price"]) ?></span>
                  </div>
                  <?php $time = calcDateExpiration($item["date_completion"]); ?>
                  <div class="lot__timer timer <?= $time["hours"] < 1 ? "timer--finishing" : "" ?>">
                    <?= "{$time["hours"]}:{$time["minutes"]}:{$time["seconds"]}" ?>
                  </div>
                </div>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else : ?>
      <h2>В категории «<span><?= $categoryName ?></span>» нет лотов</h2>
      <?php endif; ?>
    </section>
    <?php if ((int) $pages_count >= $page_items && count($lots)) : ?>
      <ul class="pagination-list">
        <?php
          $lastPage = $pages[count($pages) - 1];
          $pagePrev = ($cur_page - 1) === 0 ? 1 : ($cur_page - 1);
          $pageNext = ($cur_page + 1) > $lastPage ? $lastPage : ($cur_page + 1);
          ?>
        <li class="pagination-item pagination-item-prev">
          <?php if ($pagePrev == $cur_page) : ?>
            <a>Назад</a>
          <?php else : ?>
            <a href="/all-lots.php?id=<?= $searchCategory ?>&page=<?= $pagePrev; ?>">Назад</a>
          <?php endif; ?>
        </li>
        <?php foreach ($pages as $page) : ?>
          <li class="pagination-item <?php if ($page == $cur_page) : ?>pagination-item-active<?php endif; ?>">
            <a href="/all-lots.php?id=<?= $searchCategory ?>&page=<?= $page; ?>"><?= $page; ?></a>
          </li>
        <?php endforeach; ?>
        <li class="pagination-item pagination-item-next">
          <?php if ($pageNext == $cur_page) : ?>
            <a>Вперед</a>
          <?php else : ?>
            <a href="/all-lots.php?id=<?= $searchCategory ?>&page=<?= $pageNext; ?>">Вперед</a>
          <?php endif; ?>
        </li>
      </ul>
    <?php endif; ?>
  </div>
</main>