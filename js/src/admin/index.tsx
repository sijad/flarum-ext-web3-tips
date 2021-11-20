import app from 'flarum/admin/app';

app.initializers.add('tokenjenny-web3-tips', function() {
  app.extensionData
    .for('tokenjenny-web3-tips')
    .registerSetting(() => (
      <fieldset class="SettingsPage-token">
        <legend>{app.translator.trans('tokenjenny-web3-tips.admin.settings.token')}</legend>
      </fieldset>
    ))
    .registerSetting({
      setting: 'tokenjenny-web3-tips.token_address',
      type: 'text',
      label: app.translator.trans('tokenjenny-web3-tips.admin.settings.token_address'),
    })
    .registerSetting({
      setting: 'tokenjenny-web3-tips.token_decimals',
      type: 'number',
      label: app.translator.trans('tokenjenny-web3-tips.admin.settings.token_decimals'),
    })
    .registerSetting(() => (
      <fieldset class="SettingsPage-network">
        <legend>{app.translator.trans('tokenjenny-web3-tips.admin.settings.network')}</legend>
      </fieldset>
    ))
    .registerSetting({
      setting: 'tokenjenny-web3-tips.rpc_url',
      type: 'text',
      label: app.translator.trans('tokenjenny-web3-tips.admin.settings.rpc_url'),
    })
    .registerSetting({
      setting: 'tokenjenny-web3-tips.chain_id',
      type: 'text',
      label: app.translator.trans('tokenjenny-web3-tips.admin.settings.chain_id'),
    })
});
