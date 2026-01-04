{include file="header.tpl"}

<section class="ce-section ce-auth">
  <div class="ce-auth__wrap">
    <div class="ce-card ce-auth__card">
      <div class="ce-card__top">
        <div class="ce-card__title">Сброс пароля</div>
        <span class="ce-badge">security</span>
      </div>

      [if success]
        <div class="ce-alert ce-alert--success ce-mt-16">{success}</div>
        <div class="ce-actions ce-mt-16">
          <a class="ce-btn ce-btn--primary" href="/login">Войти</a>
        </div>
      [/if]

      [if error]
        <div class="ce-alert ce-alert--danger ce-mt-16">{error}</div>
      [/if]

      [if !success]
      <form method="post" class="ce-form ce-mt-16">
        <input type="hidden" name="csrf" value="{csrf}">
        <input type="hidden" name="token" value="{token}">

        <label class="ce-field">
          <div class="ce-field__label">Новый пароль</div>
          <input class="ce-input" type="password" name="password" minlength="8" required>
        </label>

        <label class="ce-field ce-mt-12">
          <div class="ce-field__label">Повтор пароля</div>
          <input class="ce-input" type="password" name="password2" minlength="8" required>
        </label>

        <div class="ce-actions ce-mt-16">
          <button class="ce-btn ce-btn--primary" type="submit">Сохранить</button>
          <a class="ce-btn ce-btn--ghost" href="/login">Назад</a>
        </div>
      </form>
      [/if]
    </div>
  </div>
</section>

{include file="footer.tpl"}
