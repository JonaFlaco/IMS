import { ICoords } from "../types";
export interface IHandlers {
    onMove: (e: any, shift: ICoords, pressCoords: ICoords) => void;
    onUp: (e: any) => void;
}
declare class GlobalDrag {
    private _start;
    private _handler;
    private _context;
    start(e: any, handler: IHandlers, context: any): void;
    private _moveHandler;
    private _upHandler;
}
export declare const drag: GlobalDrag;
export {};
