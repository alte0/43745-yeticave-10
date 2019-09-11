<nav class="nav">
  <ul class="nav__list container">
    <?php foreach ($categories as $item) : ?>
      <li class="nav__item <?= $categoriesIdCurrent === (int) $item["id"] ? "nav__item--current" : "" ?>">
        <a href="/all-lots.php?id=<?= $item["id"] ?>"><?= $item["name"] ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>