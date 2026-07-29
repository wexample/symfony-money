import AbstractApiRepository from '@wexample/js-api/Common/AbstractApiRepository';
import Currency from '../Entity/Currency';

export default class CurrencyRepository extends AbstractApiRepository<Currency> {
  static getEntityType() {
    return Currency;
  }
}
