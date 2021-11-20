import { extend } from 'flarum/common/extend';
import ItemList from 'flarum/common/utils/ItemList';
import app from 'flarum/forum/app';
import Button from 'flarum/common/components/Button';
import CommentPost from 'flarum/forum/components/CommentPost';
import TipModal from './components/TipModal';
import Post from 'flarum/common/models/Post';
import Model from 'flarum/common/Model';
import icon from 'flarum/helpers/icon';

app.initializers.add('tokenjenny-web3-tips', () => {
  (Post.prototype as any).tips = Model.attribute('tips');

  extend(CommentPost.prototype, 'actionItems', function(this: CommentPost, items: ItemList) {
    const post = this.attrs.post;
    const user = post.user();
    const tips = post.tips();

    if (
      post.isHidden() ||
      user.id() === app.session.user.id()
    ) {
      return;
    }

    items.add('tips',
      Button.component({
        className: 'Button Button--link',
        icon: 'fas fa-gift',
        onclick() {
          app.modal.show(TipModal, {post})
        }
      },
      app.translator.trans('tokenjenny-web3-tips.forum.post.tip_link', { count: tips }))
     );

    return items;
  });
});
