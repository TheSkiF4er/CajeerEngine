{include file="header.tpl"}

<section class="ce-section ce-auth">
  <div class="ce-auth__wrap">
    <div class="ce-card ce-auth__card">
      <div class="ce-card__top">
        <div class="ce-card__title">Подтверждение почты</div>
        <span class="ce-badge">account</span>
      </div>

      [if ok]
        <div class="ce-alert ce-alert--success ce-mt-16">Почта подтверждена. Теперь вы можете войти.</div>
        <div class="ce-actions ce-mt-16">
          <a class="ce-btn ce-btn--primary" href="/login">Войти</a>
        </div>
      [else]
        <div class="ce-alert ce-alert--danger ce-mt-16">Ссылка недействительна или истекла.</div>
        <div class="ce-actions ce-mt-16">
          <a class="ce-btn ce-btn--primary" href="/resend">Отправить письмо ещё раз</a>
          <a class="ce-btn ce-btn--ghost" href="/login">Назад</a>
        </div>
      [/if]
    </div>
  </div>
</section>

{include file="footer.tpl"}
