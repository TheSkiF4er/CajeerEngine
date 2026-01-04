{include file="header.tpl"}

<section class="ce-section ce-auth">
  <div class="ce-auth__wrap">
    <div class="ce-card ce-auth__card">
      <div class="ce-card__top">
        <div class="ce-card__title">Повторная отправка подтверждения</div>
        <span class="ce-badge">account</span>
      </div>

      [if success]
        <div class="ce-alert ce-alert--success ce-mt-16">{success}</div>
      [/if]
      [if error]
        <div class="ce-alert ce-alert--danger ce-mt-16">{error}</div>
      [/if]

      <form method="post" class="ce-form ce-mt-16">
        <input type="hidden" name="csrf" value="{csrf}">
        <label class="ce-field">
          <div class="ce-field__label">Email</div>
          <input class="ce-input" type="email" name="email" required>
        </label>
        <div class="ce-actions ce-mt-16">
          <button class="ce-btn ce-btn--primary" type="submit">Отправить</button>
          <a class="ce-btn ce-btn--ghost" href="/login">Назад</a>
        </div>
      </form>
    </div>
  </div>
</section>

{include file="footer.tpl"}
