{include file="header.tpl"}

<section class="ce-section ce-auth">
  <div class="ce-auth__wrap">
    <div class="ce-card ce-auth__card">
      <div class="ce-card__top">
        <div class="ce-card__title">Профиль</div>
        <span class="ce-badge">account</span>
      </div>

      <div class="ce-card__text ce-muted ce-mt-12">
        Email: <b>{email}</b>
      </div>

      [if verified]
        <div class="ce-alert ce-alert--success ce-mt-16">Почта подтверждена.</div>
      [else]
        <div class="ce-alert ce-alert--warn ce-mt-16">Почта не подтверждена.</div>
        <div class="ce-card ce-card--flat ce-mt-12">
          <div class="ce-card__text ce-muted">Проверьте входящие. Если письма нет — отправьте подтверждение повторно.</div>
        </div>

        <form method="post" action="/resend" class="ce-form ce-mt-12">
          <input type="hidden" name="csrf" value="{csrf}">
          <input type="hidden" name="email" value="{email}">
          <div class="ce-actions">
            <button class="ce-btn ce-btn--primary" type="submit">Отправить подтверждение</button>
          </div>
        </form>
      [/if]

      <div class="ce-actions ce-mt-16">
        <a class="ce-btn ce-btn--ghost" href="/logout">Выйти</a>
      </div>
    </div>
  </div>
</section>

{include file="footer.tpl"}
