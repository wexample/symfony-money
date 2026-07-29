import AbstractApiEntity from '@wexample/js-api/Common/AbstractApiEntity';
import schema from '../data/entity/currency.json';

export default class Currency extends AbstractApiEntity {
  static readonly entityName = 'currency';

  static retrieveEntitySchema() {
    return schema;
  }

  currencyCode!: string;
  currencySymbol!: string;
  decimals!: number;
  name?: string;
  type?: string;
}
