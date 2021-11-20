import type Mithril from 'mithril';
import Modal from 'flarum/common/components/Modal';
import Button from 'flarum/common/components/Button';

import app from 'flarum/forum/app';
import Stream from 'flarum/common/utils/Stream';
import withAttr from 'flarum/common/utils/withAttr';
import {rpcRequest, switchNetwork} from '../utils/web3';
import {parseUnits} from '../utils/units';

export default class TipModal extends Modal {
  private amount: Stream;

  oninit(vnode: Mithril.Vnode) {
    super.oninit(vnode);

    this.amount = Stream('');
  }

  className() {
    return 'FlagPostModal Modal--small';
  }

  title() {
    return app.translator.trans('tokenjenny-web3-tips.forum.tip_post.title');
  }

  content() {
    const userWallet = app.session.user.web3Account();

    if (!userWallet) {
      return (
        <div className="Modal-body">
          {app.translator.trans('tokenjenny-web3-tips.forum.tip_post.connect_wallet')}
        </div>
      );
    }

    return (
      <div className="Modal-body">
        <div className="Form Form--centered">
          <div className="Form-group">
              <input
                type="number"
                className="FormControl"
                required
                step="any"
                placeholder={app.translator.trans('tokenjenny-web3-tips.forum.tip_post.amount')}
                value={this.amount()}
                oninput={withAttr('value', this.amount)}
              />
          </div>

          <div className="Form-group">
            <Button
              className="Button Button--primary Button--block"
              type="submit"
              disabled={!this.amount()}
            >
              {app.translator.trans('tokenjenny-web3-tips.forum.tip_post.submit_button')}
            </Button>
          </div>
        </div>
      </div>
    );
  }

  onsubmit(e) {
    e.preventDefault();
    (async () => {
      const provider = (window as any).ethereum;

      const token = app.forum.attribute("web3tipsTokenAddress");
      const decimals = app.forum.attribute("web3tipsTokenDecimals");
      const chainId = app.forum.attribute("web3tipsChainId");

      if (!provider) {
        // TODO show proper alert and a download link
        alert("MetaMask not found.");
      }

      const chainIdHex = `0x${Number(chainId).toString(16)}`;

      if (provider.chainId !== chainIdHex) {
        if (!await switchNetwork(chainIdHex)) {
          // TODO add network to metamask
          alert("selected network is not supported");
          return;
        }
      }

      // TODO add account selector

      const accounts = await rpcRequest("eth_requestAccounts") as string[];

      const from = accounts[0].toLowerCase();

      const userWallet = app.session.user.web3Account().toLowerCase();

      if (from !== userWallet) {
        alert("You must send tips from your connected wallet");
        return;
      }

      const amountHex = parseUnits(this.amount(), decimals).toHexString().substr(2).padStart(64, '0');
      const post = this.attrs.post;
      const address = post.user().web3Account().substr(2).padStart(64, '0');
      // 0xa9059cbb = transfer(address,uint256)
      const input = `0xa9059cbb${address}${amountHex}`
      const params = [{to: token, from, data: input}];

      // TODO
      // const estimatedGas = await rpcRequest('eth_estimateGas', params);

      const transaction = await rpcRequest('eth_sendTransaction', params);
      console.log(transaction);

      const payload = await app.request({
        url: app.forum.attribute('apiUrl') + '/tips',
        method: 'POST',
        body: {
          data: {
            post_id: post.id(),
            transaction_id: transaction,
          },
        },
      });
    })();
  }
}

