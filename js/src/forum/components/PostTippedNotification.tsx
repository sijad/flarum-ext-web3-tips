import Notification from 'flarum/forum/components/Notification';
import { truncate } from 'flarum/common/utils/string';
import app from 'flarum/forum/app';

export default class PostTippedNotification extends Notification {
  icon() {
    return 'fas fa-gift';
  }

  href() {
    return app.route.post(this.attrs.notification.subject());
  }

  content() {
    const notification = this.attrs.notification;
    const user = notification.fromUser();

    return app.translator.trans('tokenjenny-web3-tips.forum.notifications.post_tipped_text', {user, count: 1});
  }

  excerpt() {
    return truncate(this.attrs.notification.subject().contentPlain(), 200);
  }
}
