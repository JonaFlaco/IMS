import { Text } from "./Text";
export declare class Textarea extends Text {
    private _isArray;
    setValue(value: any, silent?: boolean): void;
    getValue(): any;
    toVDOM(): any;
    private _getRows;
}
