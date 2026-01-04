{include file="header.tpl"}

<section class="ce-section ce-auth">
  <div class="ce-auth__wrap">
    <div class="ce-card ce-auth__card">
      <div class="ce-card__top">
        <div class="ce-card__title">Вход</div>
        <span class="ce-badge">account</span>
      </div>

      [if error]
        <div class="ce-alert ce-alert--danger ce-mt-16">{error}</div>
      [/if]

      <form method="post" class="ce-form ce-mt-16">
        <input type="hidden" name="csrf" value="{csrf}">
        <label class="ce-field">
          <div class="ce-field__label">Email</div>
          <input class="ce-input" type="email" name="email" value="{prefill_email}" required>
        </label>

        <label class="ce-field ce-mt-12">
          <div class="ce-field__label">Пароль</div>
          <input class="ce-input" type="password" name="password" required>
        </label>

        <div class="ce-actions ce-mt-16">
          <button class="ce-btn ce-btn--primary" type="submit">Войти</button>
          <a class="ce-btn ce-btn--ghost" href="/register">Регистрация</a>
        </div>

        <div class="ce-actions ce-mt-12">
          <a class="ce-link" href="/forgot">Забыли пароль?</a>
          <a class="ce-link" href="/resend">Повторить подтверждение</a>
        </div>
      </form>
    </div>
  </div>
</section>

{include file="footer.tpl"}
