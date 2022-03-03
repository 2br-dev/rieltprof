{* Для работы во всплывающих окнах, должен подключаться в layout.tpl *}
{addcss file="%users%/verification.css"}
{$verification_engine = $field->getVerificationEngine()}
{$verification_engine->getVerificationFormView()}